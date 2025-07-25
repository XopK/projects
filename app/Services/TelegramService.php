<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TelegramService
{

    public function __construct()
    {
    }

    public function generateTelegramToken($userId)
    {
        $token = Str::uuid()->toString();

        $user = User::find($userId);

        if ($user) {
            $user->token_telegram = $token;
            $user->save();

            return $token;
        }

        Log::warning('User not found for generating token', ['userId' => $userId]);

        return null;

    }

    public function generateTelegramLink($userId)
    {
        Log::info('Generating Telegram link for user', ['userId' => $userId]);

        $botName = config('services.telegram.bot_name');

        $user = User::find($userId);

        if ($user && $user->token_telegram) {

            $telegramLink = 'https://t.me/' . $botName . '?start=' . $user->token_telegram;

            return $telegramLink;
        }

        return null;
    }

    public function checkResponse($response)
    {
        if (isset($response['message']) && strpos($response['message']['text'], '/start') === 0) {

            $text = $response['message']['text'];
            $parts = explode(' ', $text);
            $token = $parts[1] ?? null;

            if ($token) {
                $user = User::where('token_telegram', $token)->first();

                if ($user) {
                    $chatId = $response['message']['chat']['id'];
                    $telegram_username = $response['message']['from']['username'];

                    $alreadyLinked = User::where('chat_id_telegram', $chatId)->where('id', '!=', $user->id)->exists();

                    if ($alreadyLinked) {
                        $this->sendMessage($chatId, '❌ Этот Telegram-аккаунт уже привязан к другому профилю на сайте.');
                        $user->token_telegram = null;
                        $user->save();
                        return null;
                    }

                    $user->chat_id_telegram = $chatId;
                    $user->token_telegram = null;
                    $user->username_telegram = $telegram_username;
                    $user->save();

                    $message = "🎉 Аккаунт успешно привязан!\n\nТеперь вы будете получать уведомления здесь.\n\nНе забудьте включить уведомления\nв Telegram и на сайте, чтобы получать уведомления.";

                    $this->sendPhoto($chatId, 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/public/resources/telegram/success_tg.png', $message);
                } else {
                    Log::warning('User not found for token', ['token' => $token]);
                    return null;
                }
            }
        }
    }

    public function sendMessage($chatId, $message, $parseMode = 'Markdown')
    {
        $token = config('services.telegram.bot_token');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => $parseMode,
        ];

        $response = Http::post($url, $params);

        if ($response->successful()) {
            Log::info('Message successfully sent', ['chatId' => $chatId, 'response' => $response->json()]);
            return $response->json();
        }

        Log::error('Failed to send message', ['chatId' => $chatId, 'response' => $response->body()]);

        return null;
    }

    public function sendPhoto($chatId, $photoUrl, $caption = '', $parseMode = 'Markdown')
    {
        $token = config('services.telegram.bot_token');
        $url = "https://api.telegram.org/bot{$token}/sendPhoto";

        $params = [
            'chat_id' => $chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => $parseMode,
        ];

        $response = Http::post($url, $params);

        if ($response->successful()) {
            Log::info('Photo successfully sent', ['chatId' => $chatId, 'response' => $response->json()]);
            return $response->json();
        }

        Log::error('Failed to send photo', ['chatId' => $chatId, 'response' => $response->body()]);

        return null;
    }

}
