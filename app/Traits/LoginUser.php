<?php

namespace App\Traits;


trait LoginUser
{
    use Response;

    public function login_with_token($user, $time = 6 * 24 * 30)
    {
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('timwoork_token', $token, $time);
        return $this->with_cookie('success', $cookie);
    }
}
