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

Broadcast::channel('receiver.{id}', function ($user, $id) {
    if ($user->id == $id) {
        return $user;
    }
});

Broadcast::channel('notify.{id}', function ($user, $id) {
    if ($user->id == $id) {
        return $user;
    }
});

Broadcast::channel('currency', function () {
    return true;
});
