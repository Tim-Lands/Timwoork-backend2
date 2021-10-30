<?php

namespace App\Traits;


trait LoginUser
{

    public function login_with_token($user)
    {
        $token = $user->createToken('token')->plainTextToken;
        //$cookie = cookie('timwoork_token', $token, $time);
        return response()->success('لقد تم تسجيل الدخول بنجاح', $token);
    }
}
