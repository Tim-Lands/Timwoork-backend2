<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\Dashboard\Auth\LoginRequest;
use App\Models\User;
use App\Traits\LoginUser;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use LoginUser;
    public function login(LoginRequest $request)
    {
        // تتم عملية التسجيل الدخول بواسطة البريد الالكتروني أو اسم المستخدم أو رقم الهاتف
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->orWhere('phone', $request->username)
            ->first();
        // في حالة عدم وجود المستخدم في قاعدة البيانات أو عدم تطابق كلمة المرور المحفوظة مع كلمة المرور المرسلة
        // يتم إرسال رسالة عدم صحة البيانات
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->error('المعلومات التي أدخلتها خاطئة', 401);
        }
        Auth::login($user);
        // في حالة صحة البيانات سيتم إنشاء توكن وتخزينه في جلسة كوكي وإرساله مع كل طلب
        return $this->login_with_token($user);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
    public function logout()
    {
        $cookie = cookie()->forget('timwoork_token');
        return response([
            'msg' => "Success"
        ])->withCookie($cookie);
    }

    /*************************Socialite Login *************************/

    /**
     * @var \Laravel\Socialite\Facades\Socialite
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }


    public function handleProviderCallback($provider)
    {
        try {
            $s_user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->error('المعلومات التي أدخلتها خاطئة', 401);
        }

        $user = User::create();
        $user->providers()->create([
            'provider' => $provider,
            'provider_id' => $s_user->getId()
        ]);
        return $this->login_with_token($user);
    }
    /**************************************************************** */
}
