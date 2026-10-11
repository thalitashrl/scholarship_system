<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Models\LiveChat::find($chatId);
    if (!$chat) {
        return false;
    }
    return (int) $user->id === (int) $chat->mahasiswa_id 
        || (int) $user->id === (int) $chat->staff_id 
        || $user->hasRole('admin');
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
