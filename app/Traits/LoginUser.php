<?php

namespace App\Traits;


trait LoginUser
{

    public function login_with_token($user, $time = 6 * 24 * 30)
    {
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('timwoork_token', $token, $time);
        return response()->withCookie('لقد تم تسجيل الدخول بنجاح', $cookie);
    }
}
