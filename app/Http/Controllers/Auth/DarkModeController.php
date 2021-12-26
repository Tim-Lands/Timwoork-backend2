<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
            return response()->success('لقد تم تغيير الوضع إلى وضع نهاري');
        } else {
            $user->profile->dark_mode = true;
            $user->profile->save();
            return response()->success('لقد تم تغيير الوضع إلى وضع ليلي');
        }
    }
}
