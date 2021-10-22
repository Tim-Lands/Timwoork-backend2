<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Traits\VerificationEmailTrait;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendVerifyRequest;
use App\Http\Requests\VerifyEmailRequest;

use App\Models\User;
use App\Models\VerifyEmailCode;

use App\Events\VerifyEmail;
use App\Traits\LoginUser;

class RegisterController extends Controller
{
    use VerificationEmailTrait, LoginUser;

    public function register(RegisterRequest $request)
    {
        // تسجيل مستخدم جديد 
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // إنشاء رمز تفعيل البريد اﻹلكتروني  
        $this->store_code_bin($user);

        // إرسال رمز التفعيل إلى البريد الإلكتروني
        event(new VerifyEmail($user));

        // تسجيل الدخول للمستخدم الجديد  
        return $this->login_with_token($user);
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {

        return $this->verify_email($request->email, $request->code);
    }


    public function resend_verify_code(ResendVerifyRequest $request)
    {
        return $this->resend_code($request->email);
    }
}
