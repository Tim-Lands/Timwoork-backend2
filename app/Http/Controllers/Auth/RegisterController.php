<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Traits\VerificationEmailTrait;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendVerifyRequest;
use App\Http\Requests\VerifyEmailRequest;

use App\Models\User;
use App\Models\Country;

use App\Events\VerifyEmail;
use App\Traits\LoginUser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RegisterController extends Controller
{
    use VerificationEmailTrait, LoginUser;

    public function register(RegisterRequest $request)
    {
        // تسجيل مستخدم جديد
        try {
            DB::beginTransaction();
            $code_phones = array();
            if (Cache::has('code_phones')) {
                $code_phones = Cache::get('code_phones');
            } else {
                $temp_arr = array();
                $data = Country::all()->groupBy('code_phone');
                $code_phones = $data;
                Cache::add('code_phones', $code_phones);
            }
            if (!isset($code_phones[$request->code_phone]))
                throw new Exception("يجب إختيار كود هاتف متاح");
            $user = User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
            ]);
            // انشاء مستخدم جديد
            $user->profile()->create([
                'avatar' => url('avatars/avatar.png'),
                'lang' => 'ar',
                'badge_id' => 1,
                'level_id' => 1,
            ]);
            // انشاء محفظة للمستخدم

            $user->profile->wallet()->create([]);
            $this->store_code_bin($user);
            // إرسال رمز التفعيل إلى البريد الإلكتروني
            event(new VerifyEmail($user));

            // تسجيل المستخدم الجديد في chatEngine

            //$this->createChatEngineUser($user);
            // تسجيل الدخول للمستخدم الجديد
            Auth::login($user);
            // إنهاء العملية
            DB::commit();
            // وإرسال التوكن عبر الكوكي
            return $this->login_with_token($user);
        } catch (Exception $ex) {
            DB::rollback();
            echo $ex;
            // رسالة خطأ
            return response()->error(__('messages.errors.error_database'), 403);
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

    /*     public function createChatEngineUser($user)
    {
        return Http::withHeaders([
            'PRIVATE-KEY' => '2805db84-87b8-4fef-bb94-7e3c5fd22b37'
        ])->asForm()->put('https://api.chatengine.io/users/', [
            'username' => $user->username,
            'secret' => $user->email + $user->id,
        ]);
    } */
}
