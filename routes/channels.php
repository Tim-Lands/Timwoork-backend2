<?php

use App\Models\Conversation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat', function ($user) {
    return $user;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{id}', function ($user, $id) {
    $conversation = Conversation::findOrFail($id);
    $user_id = Auth::id();
    $members = $conversation->members()->where('user_id', '<>', $user_id);
    $ids =  Arr::pluck($members, 'id');
    if (in_array($user->id, $ids)) {
        return $user;
    }
});
