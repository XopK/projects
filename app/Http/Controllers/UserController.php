<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdate;

use App\Models\Group;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\Notifier;
use App\Services\PhoneService;
use App\Services\PhotoService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    protected $telegramService;
    protected $PhotoService;
    protected $PhoneService;
    protected $notifier;

    public function __construct(TelegramService $telegramService, PhotoService $PhotoService, PhoneService $PhoneService, Notifier $notifier)
    {
        $this->telegramService = $telegramService;
        $this->PhotoService = $PhotoService;
        $this->PhoneService = $PhoneService;
        $this->notifier = $notifier;
    }

    public function index()
    {
        return view('pages.profile');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(UserUpdate $request)
    {
        try {
            $user = $request->user();
            $phone = $request->phone;

            if ($phone !== $user->phone) {
                if (!$request->code_phone) {
                    return redirect()->back()->withInput()->with('error', 'Введите код подтверждения телефона.');
                }

                $existPhoneVerify = PhoneVerification::where('phone', $phone)
                    ->where('code', $request->code_phone)
                    ->where('expires_at', '>=', now())
                    ->first();

                if (!$existPhoneVerify) {
                    return redirect()->back()->withInput()->with('error', 'Неверный или просроченный код подтверждения.');
                }

                $existPhoneVerify->delete();
            }

            $user->fill([
                'name' => $request->name,
                'nickname' => $request->nickname,
                'email' => $request->email,
                'birthday' => $request->birthday,
                'phone' => $request->phone,
            ]);

            if ($user->save()) {
                return redirect()->back()->with('success', 'Данные успешно изменены!');
            } else {
                return redirect()->back()->with('error', 'Ошибка обновления, попробуйте ещё раз!');
            }

        } catch (\Exception $exception) {

            Log::channel('errorlog')->error('Ошибка обновления пользователя с ID ' . $user->id . ': ' . $exception->getMessage());

            abort(500);
        }

    }

    public function favorites()
    {
        $query = Auth::user()->favorites()->with('group');

        if ($search = request('search')) {
            $query->whereHas('group', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        if (in_array($sortField, ['created_at']) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $favorites = $query->paginate(6)->withQueryString();

        return view('pages.favorite', compact('favorites'));
    }

    public function groups()
    {
        $query = Auth::user()->list_groups();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('categories', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $sortField = request('sort_field', 'pivot_created_at');
        $sortDirection = request('sort_direction', 'desc');

        if ($sortField === 'pivot_created_at' && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderByPivot('created_at', $sortDirection);
        } elseif (in_array($sortField, ['created_at', 'title']) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $groups = $query->paginate(6)->withQueryString();

        return view('pages.groups_profile', ['groups' => $groups]);
    }

    public function my_groups()
    {
        $user = Auth::user();

        $query = $user->groups();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('categories', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        if (in_array($sortField, ['created_at', 'title']) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $groups = $query->paginate(6)->withQueryString();

        return view('pages.my_groups', [
            'groups' => $groups,
        ]);
    }


    public function telegram()
    {
        $user = Auth::user();

        if ($user->token_telegram == null || $user->chat_id_telegram == null) {
            $this->telegramService->generateTelegramToken($user->id);
        }

        return redirect()->to($this->telegramService->generateTelegramLink($user->id));

    }

    public function unlinkTelegram()
    {
        try {
            $user = Auth::user();

            $user->token_telegram = null;
            $this->telegramService->sendPhoto(
                $user->chat_id_telegram,
                'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/public/resources/telegram/unlink_tg.png',
                'Вы успешно отвязали свой аккаунт!'
            );
            $user->chat_id_telegram = null;
            $user->username_telegram = null;
            $user->save();

            return redirect()->back()->with('success', 'Вы успешно отвязали свой аккаунт!');
        } catch (\Exception $exception) {
            Log::error('Ошибка отвязки аккаунта: ' . $exception->getMessage());
            return redirect()->back()->with('error', 'Ошибка выполнения команды!');
        }

    }

    public function callbackTelegram(Request $request)
    {
        if ($this->telegramService->checkResponse($request->all())) {
            return true;
        }

        return false;
    }

    public function avatar_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $user->photo_profile = $this->PhotoService->upload($request->file('avatar'), 'user_avatars', 'public_s3');

        if ($user->save()) {
            return response()->json([
                'message' => 'Аватарка добавлена',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка загрузки аватарки',
            ], 500);
        }
    }

    public function markRead(Request $request)
    {
        $idNotify = $request->get('idNotify');

        $notification = auth()->user()->unreadNotifications()->findOrFail($idNotify);
        $notification->markAsRead();

        return redirect($notification->data['link']);
    }

    public function notifySetting(Request $request, string $field)
    {
        $allowedFields = ['notify_tg', 'notify_site', 'notify_email'];

        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'message' => 'Данного поля не существует!'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации'
            ], 402);
        }

        auth()->user()->update([
            $field => $request->boolean('value'),
        ]);

        return response()->noContent();
    }

    public function notifications()
    {
        $unreadNotifications = auth()->user()->unreadNotifications;
        $readNotifications = auth()->user()->readNotifications;

        return view('pages.notifications', [
            'unreadNotifications' => $unreadNotifications,
            'readNotifications' => $readNotifications
        ]);
    }

    public function notificationsAllRead()
    {
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    public function updatePhone(Request $request)
    {
        try {
            $code = rand(1000, 9999);
            $token = Str::uuid();
            $user = auth()->user();

            $exists = PhoneVerification::where('phone', $request->phone)->exists();

            if ($exists) {
                return response()->json([
                    'success' => true,
                ]);
            }

            PhoneVerification::create([
                'session_token' => $token,
                'phone' => $request->phone,
                'code' => $code,
                'data' => json_encode([
                    'user_id' => $user->id,
                ]),
                'expires_at' => now()->addMinutes(15),
            ]);

            $number = preg_replace('/\D+/', '', $request->phone);
            $message = 'Ваш код подтверждения для смены номера телефона: ' . $code;

            $this->PhoneService->sendSMS($number, $message);

            return response()->json([
                'success' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка отправки кода: ' . $e->getMessage());
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|same:new_password',
        ]);

        $user = auth()->user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Пароль успешно изменен!');
        }

        return redirect()->back()->with('error', 'Ошибка изменения пароля!');

    }

    public function userList(Group $group)
    {
        $users = $group->list_users()
            ->select('users.id', 'users.name', 'users.nickname', 'users.photo_profile', 'list_users.status_confirm')->get();

        return response()->json(['users' => $users]);
    }

    public function userListStatus(Request $request, Group $group)
    {
        $userId = $request->input('userId');
        $action = $request->input('action');

        $user = $group->list_users()->where('users.id', $userId)->first();
        $owner = User::findOrFail($group->user_id);

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден в группе'], 404);
        }

        if ($action === 'apply') {
            $group->list_users()->updateExistingPivot($userId, ['status_confirm' => 1]);
            $this->notifier->announcement($user, $owner, $group, 'apply_register_user_on_group');

            return response()->json([
                'action' => 'applied',
                'userId' => $userId
            ]);
        }
        if ($action === 'deny') {
            $group->list_users()->detach($userId);
            $this->notifier->announcement($user, $owner, $group, 'reject_register_user_on_group');
            return response()->json([
                'action' => 'denied',
                'userId' => $userId
            ]);
        }
        if ($action === 'return') {
            $group->list_users()->updateExistingPivot($userId, ['status_confirm' => 0]);
            $this->notifier->announcement($user, $owner, $group, 'returned_register_user_on_group');
            return response()->json([
                'action' => 'returned',
                'userId' => $userId
            ]);
        }

        return response()->json(['error' => 'Неизвестное действие'], 400);
    }

}
