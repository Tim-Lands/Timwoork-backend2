<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    public function online(User $user)
    {
        $user->status = true;
        $user->save();
        broadcast(new UserOnline($user));
    }

    public function offilne(User $user)
    {
        $user->status = false;
        $user->save();
        broadcast(new UserOffline($user));
    }
}
