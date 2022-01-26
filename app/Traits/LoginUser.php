<?php

namespace App\Traits;


trait LoginUser
{

    public function login_with_token($user)
    {
        $token = $user->createToken('token')->plainTextToken;
        //$cookie = cookie('timwoork_token', $token, $time);
        // تغيير حالة اليوزر الى اونلاين
        $user->status = true;
        $user->save();
        return response()->success('لقد تم تسجيل الدخول بنجاح', [
            'token' => $token,
            'is_verified' => $user->email_verified_at ? true : false,
            'step' => $user->profile->steps,
        ]);
    }
}
