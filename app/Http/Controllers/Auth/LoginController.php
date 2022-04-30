<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\Dashboard\Auth\LoginRequest;
use App\Http\Requests\SocialProviderRequest;
use App\Models\User;
use App\Traits\LoginUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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
            return response()->error(__("messages.user.error_login"), Response::HTTP_UNAUTHORIZED);
        }
        Auth::login($user);
        // في حالة صحة البيانات سيتم إنشاء توكن وتخزينه في جلسة كوكي وإرساله مع كل طلب
        return $this->login_with_token($user);
    }

    public function me(Request $request)
    {
        $paginate = $request->query('paginate') ?? 10;
        $user =  $request->user()->load([
            'profile.profile_seller.badge',
            'profile.profile_seller.level',
            'profile.profile_seller.skills',
            'profile.badge',
            'profile.level',
            'profile.country',
            'profile.wallet' => function ($q) {
                return $q->with('activities');
            },
        ]);

        // make some columns hidden in response
        $notifications_count = $user->unreadNotifications->count();
        $msg_count = $this->getUnreadMessagesCount($user);
        $cart_items_count = $this->getCartItemsCount($user);
        $data = [
            'user_details' => $user,
            'unread_messages_count' => $msg_count,
            'unread_notifications_count' => $notifications_count,
            'cart_items_count' => $cart_items_count
        ];
        return response()->json($data, Response::HTTP_OK);
    }
    /**
     * logout_all => تسجيل الخروج من جميع جلسات المستخدم
     *
     * @return void
     */
    public function logout_all()
    {
        // get current user
        $user = Auth::user();
        // delete all user tokens
        $user->tokens()->delete();
        // تغيير حالة المستخدم الى اوفلاين
        $user->status = false;
        $user->save();
        // send success message to frontend
        return response()->success(__("messages.user.logout"));
    }

    /**
     * logout_user => تسجيل الخروج للمستخدم
     *
     * @return void
     */
    public function logout_user()
    {
        $user = Auth::user(); //or Auth::user()
        // Revoke current user token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        // تغيير حالة المستخدم الى اوفلاين
        $user->status = false;
        $user->save();
        return response()->success(__('messages.user.logout'));
    }

    /**
     * get user unread messeges count
     */
    public function getUnreadMessagesCount($user)
    {
        $count = $user->conversations->loadCount(['messages' => function ($q) {
            $q->whereNull('read_at')
                ->where('user_id', '<>', Auth::id());
        }])->sum('messages_count');
        return $count;
    }

    /**
     * Get user cart items count
     */
    public function getCartItemsCount($user)
    {
        if ($user->carts()->activeCart()->first()) {
            return  $user->carts()->activeCart()->first()->cart_items()->get()->count();
        } else {
            return 0;
        }
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
            Auth::login($user);
            return $this->login_with_token($user);
        } else {
            $email_exists = User::select('email')->where('email', $request->email)->first();
            if ($email_exists) {
                return response()->error(__("messages.user.email_already"), Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {

                // وإلا قم بإنشاء مستخدم جديد
                try {
                    DB::beginTransaction();
                    $user = User::create([
                        'email' => $request->email,
                        'username' => $request->username,
                        'email_verified_at' => now(),
                    ]);

                    // وبروفايل جديد يحمل الاسم والصورة إن وجدت
                    $user->profile()->create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'full_name' => $request->full_name,
                        'avatar_url' => $request->avatar,
                        'badge_id' => 1,
                        'level_id' => 1,
                        'lang' => 'ar',
                    ]);

                    // انشاء محفظة للمستخدم
                    /*
                    $user->profile->wallet()->create([]);
                    // إنشاء حساب بايبال
                    $user->profile->paypal_account()->create([]);
                    // إنشاء حساب وايز
                    $user->profile->wise_account()->create([]);
                    // إنشاء معلومات حساب بنكي
                    $user->profile->bank_account()->create([]);
                    // إنشاء معلومات حوالة بنكية
                    $user->profile->bank_transfer_detail()->create([]);*/
                    // إنشاء رمز تفعيل البريد اﻹلكتروني
                    // تسجيل اسم المزوّد و المعرّف الخاص بالمستخدم في المزود الخاص به
                    $user->providers()->create([
                        'provider' => $provider,
                        'provider_id' => $request->provider_id
                    ]);
                    //$this->createChatEngineUser($user);

                    DB::commit();
                    // عملية تسجيل الدخول بعد نجاح العملية
                    Auth::login($user);
                    return $this->login_with_token($user);
                } catch (Exception $ex) {
                    DB::rollBack();
                    return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
                }
            }
        }
    }
    /**************************************************************** */

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
