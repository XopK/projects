<?php

namespace App\Services;

use App\Models\Chat;

class ChatService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createChat()
    {
        $chat = new Chat();
        $chat->save();
        $chat->name_chat = "Чат №{$chat->id}";
        $chat->save();

        return $chat->id;
    }

    public function linkChatUsers(array $users)
    {
        sort($users);

        $chat = Chat::whereHas('users', function ($query) use ($users) {
            $query->whereIn('user_id', $users);
        }, '=', count($users))
            ->first();

        if (!$chat) {
            $chat_id = $this->createChat();
            $chat = Chat::find($chat_id);
            $chat->users()->attach($users);
        }

        return $chat->id;
    }
}
