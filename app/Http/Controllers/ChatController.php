<?php

namespace App\Http\Controllers;

use App\Events\Chat\ReadMessages;
use App\Events\Chat\SendMessage;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use App\Services\ChatService;
use App\Services\PhotoService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{

    protected $chatService;
    protected $telegramService;
    protected $photoService;

    public function __construct(ChatService $chatService, TelegramService $telegramService, PhotoService $photoService)
    {
        $this->chatService = $chatService;
        $this->telegramService = $telegramService;
        $this->photoService = $photoService;
    }

    public function index(Request $request)
    {
        $targetUserId = (int)$request->get('user');
        $authUserId = auth()->id();

        if ($targetUserId && $authUserId === $targetUserId) {
            return redirect()->route('chat');
        }

        if ($targetUserId && $authUserId !== $targetUserId) {
            $this->chatService->linkChatUsers([$authUserId, $targetUserId]);
        }

        return view('pages.chat');
    }

    public function getChats()
    {
        $user = auth()->user();
        $chats = $user->chats()
            ->whereHas('messages')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($chats as $chat) {
            $receiver = $chat->users->where('id', '!=', $user->id)->first();

            $chat->receiver = $receiver;
            unset($chat->users);
            $chat->you = $user;

            $lastMessage = $chat->messages->last();
            $chat->last_message = $lastMessage ?? null;

            unset($chat->messages);

        }

        return response()->json($chats, 200);
    }

    public function getMessages(Request $request)
    {
        $you = auth()->id();
        $user = (int)$request->get('user');

        if (!$user || $you === $user) {
            return response()->json([
                'message' => $you === $user ? 'incorrect request' : 'incomplete request',
                'status' => $you === $user ? 400 : 206,
            ], $you === $user ? 400 : 206);
        }

        $chat = Chat::whereHas('users', function ($query) use ($you, $user) {
            $query->whereIn('user_id', [$you, $user]);
        })
            ->withCount(['users' => function ($query) use ($you, $user) {
                $query->whereIn('user_id', [$you, $user]);
            }])
            ->having('users_count', '=', 2)
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$chat) {
            return response()->json(['chat_id' => false], 204);
        }

        $users = User::whereIn('id', [$you, $user])
            ->get()
            ->keyBy('id');

        $groupedMessages = [
            'mine' => [],
            'other' => [],
            'user_other' => $users[$user]->makeHidden(['phone', 'email', 'email_verified_at', 'created_at', 'updated_at']),
            'user_mine' => $users[$you]->makeHidden(['phone', 'email', 'email_verified_at', 'created_at', 'updated_at']),
            'chat_id' => $chat->id,
        ];

        foreach ($chat->messages as $message) {
            if ($message->user_id == $you) {
                $groupedMessages['mine'][] = $message;
            } else {
                $groupedMessages['other'][] = $message;
            }
        }

        return response()->json($groupedMessages, 200);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'chatId' => 'required|integer',
            'files' => 'nullable|array',
        ], [
            'message.required' => 'Введите сообщение',
            'message.string' => 'Сообщение некорректно',
            'message.max' => 'Превышен максимальный лимит символов',
        ]);

        try {
            $you = auth()->user();

            $files = $request->file('files');
            $messageFile = [];
            if ($files) {
                foreach ($files as $file) {
                    $path = $this->photoService->upload($file, 'messages', 'public_s3');
                    $messageFile[] = $path;
                }
            }

            $message = Message::create([
                'user_id' => $you->id,
                'message' => $request->message,
                'is_read' => 0,
                'chat_id' => $request->chatId,
                'file' => count($messageFile) ? json_encode($messageFile) : null,
            ]);

            event(new SendMessage($message));

            /*отправка уведомлений*/
            $chat = Chat::with('users')->findOrFail($request->chatId);
            $recipient = $chat->users->where('id', '!=', $you->id)->first();

            $lastNotification = $recipient->notifications()->where('type', NewMessage::class)->whereNull('read_at')->latest()->first();

            $shouldNotify = true;

            if ($lastNotification) {
                $lastSent = $lastNotification->created_at;
                $shouldNotify = $lastSent->diffInMinutes(now()) > 5;
            }

            if ($shouldNotify) {
                $notifyMessage = "Вам пришло новое сообщение!";
                $notifyTitle = 'От пользователя: ' . $you->name . ' ' . $request->nickname;
                $link = route('chat', ['user' => $you->id]);

                $recipient->notify(new NewMessage($notifyTitle, $notifyMessage, $link));
            }

            /*конец отправки уведомлений*/
            $isOnline = Cache::remember("user_online_{$recipient->id}", 60, function () use ($recipient) {
                return DB::table('sessions')
                    ->where('user_id', $recipient->id)
                    ->where('last_activity', '>=', now()->subMinutes(1)->timestamp)
                    ->exists();
            });

            /*отправка уведомлений ТГ*/
            if ($recipient->notify_tg && $recipient->chat_id_telegram && !$isOnline) {
                try {
                    $linkUser = route('chat', ['user' => $you->id]);
                    $telegramMessage = "Вам пришло новое сообщение!\n" . "от пользователя *{$you->name} {$you->nickname}*\n\n[Открыть сообщение]($linkUser)";
                    $this->telegramService->sendPhoto(
                        $recipient->chat_id_telegram,
                        'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/public/resources/telegram/new_message_tg.png',
                        $telegramMessage
                    );
                } catch (\Throwable $e) {
                    Log::warning("Ошибка при отправке в Telegram: " . $e->getMessage());
                }
            }
            /*конец отправки уведомлений ТГ*/

            return response()->json($message, 200);

        } catch (\Exception $e) {
            Log::error('Ошибка при отправке сообщения:' . $e->getMessage());

            return response()->json([
                'message' => 'Произошла ошибка при отправке сообщения',
                'status' => 500,
            ], 500);
        }

    }

    public function markReadMessage(Request $request)
    {
        $you = auth()->user();
        $message = Message::find($request->message_id);
        $message->is_read = 1;
        $message->save();

        broadcast(new ReadMessages($message))->toOthers();

        return response()->noContent();

    }

    public function fileUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|array|min:1|max:5',
            'file.*' => 'file|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $paths = [];

        foreach ($validated['file'] as $file) {
            $path = $this->photoService->upload($file, 'messages', 'public_s3');
            $paths[] = $path;
        }

        return response()->json([
            'message' => 'Файлы успешно загружены',
            'paths' => $paths,
        ]);
    }
}
