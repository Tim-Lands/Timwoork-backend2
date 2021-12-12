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
        $user =  $request->user()->load([
            'profile.profile_seller.badge',
            'profile.profile_seller.level',
            'profile.badge',
            'profile.level'
        ]);

        // make some columns hidden in response
        $user->makeHidden('created_at', 'updated_at', 'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at');
        $user->profile->makeHidden(['steps', 'user_id', 'badge_id', 'level_id', 'country_id', 'created_at', 'updated_at']);
        $user->profile->badge->makeHidden(['name_en', 'name_fr', 'created_at', 'updated_at']);
        $user->profile->level->makeHidden(['name_en', 'name_fr', 'number_developments', 'price_developments', 'number_sales', 'created_at', 'updated_at']);
        $user->profile->profile_seller->makeHidden(['steps', 'badge_id', 'level_id', 'profile_id', 'created_at', 'updated_at']);
        $user->profile->profile_seller->skills->makeHidden(['name_en', 'name_fr', 'pivot', 'created_at', 'updated_at']);
        $user->profile->profile_seller->badge->makeHidden(['name_en', 'name_fr', 'created_at', 'updated_at']);
        $user->profile->profile_seller->level->makeHidden(['name_en', 'name_fr', 'value_bayer', 'created_at', 'updated_at']);

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
