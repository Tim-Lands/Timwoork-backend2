<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DarkModeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $user = Auth::user();
        $mode =  $user->profile->dark_mode;
        if ($mode) {
            $user->profile->dark_mode = false;
            $user->profile->save();
            return response()->success(__("messages.mode.active_dark_mode"));
        } else {
            $user->profile->dark_mode = true;
            $user->profile->save();
            return response()->success(__("messages.mode.disactive_dark_mode"));
        }
    }
}
