<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarkModeController extends Controller
{
    public function lightMode()
    {
        $user = Auth::user();
        $user->profile->dark_mode = false;
        $user->profile->save();
        return response()->success('لقد تم تغيير الوضع إلى وضع نهاري');
    }

    public function darkMode()
    {
        $user = Auth::user();
        $user->profile->dark_mode = true;
        $user->profile->save();
        return response()->success('لقد تم تغيير الوضع إلى وضع ليلي');
    }
}
