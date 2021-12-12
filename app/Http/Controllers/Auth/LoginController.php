<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\Dashboard\Auth\LoginRequest;
use App\Http\Requests\SocialProviderRequest;
use App\Models\User;
use App\Traits\LoginUser;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $user =  $request->user()->load([
            'profile.profile_seller.badge',
            'profile.profile_seller.level',
            'profile.badge',
            'profile.level'
        ]);

        // make some columns hidden in response

        $msg_count = $this->getUnreadMessagesCount($user);
        $data = [
            'user_details' => $user,
            'msg_unread_count' => $msg_count
        ];
        return response()->json($data, 200);
    }
    public function logout()
    {
        $cookie = cookie()->forget('timwoork_token');
        return response([
            'msg' => "Success"
        ])->withCookie($cookie);
    }

    /**
     * get user unread messeges count   
     */
    public function getUnreadMessagesCount($user)
    {
        $count = $user->conversations->loadCount(['messages' => function ($q) {
            $q->whereNull('read_at')
                ->where('user_id', '!=', Auth::id());
        }])->sum('messages_count');
        return $count;
    }

    /*************************Socialite Login *************************/

    public function handleProviderCallback($provider, SocialProviderRequest $request)
    {
        // البحث عن مستخدم مسجّل سابقا بأحد مواقع التواصل الاجتماعي
        //
        $user = User::whereHas('providers', function ($q) use ($provider, $request) {
            $q->where('provider_id', $request->provider_id)
                ->where('provider', $provider);
        })->first();
        // في حالة وجود مستخدم سابق مسجل سيتم تسجيل دخوله مباشرة
        if ($user) {
            return $this->login_with_token($user);
        } else {
            // وإلا قم بإنشاء مستخدم جديد 
            try {
                DB::transaction();
                $user = User::create([
                    'email' => $request->email,
                    'email_verified_at' => now(),
                ]);

                // وبروفايل جديد يحمل الاسم والصورة إن وجدت
                $user->profile()->create([
                    'first_name' => $request->first_name,
                    'avatar' => $request->avatar
                ]);

                // تسجيل اسم المزوّد و المعرّف الخاص بالمستخدم في المزود الخاص به 
                $user->providers()->create([
                    'provider' => $provider,
                    'provider_id' => $request->provider_id
                ]);
                DB::commit();
                // عملية تسجيل الدخول بعد نجاح العملية
                return $this->login_with_token($user);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        }
    }
    /**************************************************************** */
}
