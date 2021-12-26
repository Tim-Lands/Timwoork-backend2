<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DarkModeController extends Controller
{
    public function lightMode(User $user)
    {
        $user->profile->dark_mode = false;
        $user->profile->save();
        return response()->success('لقد تم تغيير الوضع إلى وضع نهاري');
    }

    public function darkMode(User $user)
    {
        $user->profile->dark_mode = true;
        $user->profile->save();
        return response()->success('لقد تم تغيير الوضع إلى وضع ليلي');
    }
}
