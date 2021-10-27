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
use App\Models\Profile;
use App\Traits\LoginUser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use VerificationEmailTrait, LoginUser;

    public function register(RegisterRequest $request)
    {
        // تسجيل مستخدم جديد 
        try {
            DB::beginTransaction();
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $user->profile()->create();
            // إنشاء رمز تفعيل البريد اﻹلكتروني 

            $this->store_code_bin($user);
            // إرسال رمز التفعيل إلى البريد الإلكتروني
            event(new VerifyEmail($user));
            // إنهاء العملية
            DB::commit();
            // تسجيل الدخول للمستخدم الجديد  
            Auth::login($user);
            // وإرسال التوكن عبر الكوكي
            return $this->login_with_token($user);
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
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
