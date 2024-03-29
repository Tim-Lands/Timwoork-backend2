<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePortfolioRequest;
use App\Http\Requests\ProfileStepOneRequest;
use App\Http\Requests\ProfileStepThreeRequest;
use App\Http\Requests\ProfileStepTwoRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum','abilities:user'])->except('show','show1', 'index');
    }
    /**
     * show => اظهار بروفايل المشتري
     *
     * @param  mixed $username
     * @return void
     */
    public function show($username, Request $request)
    {
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        // البحث في قاعدة البيانات عن اسم المستخدم
        $user = User::where('username', $username)
            ->orWhere('id', $username)
            ->with([
                'profile.profile_seller.badge',
                'profile.profile_seller.level',
                'profile' => function ($query) {
                    $query->with('profile_seller', function ($query) {
                        $query->with('products', function ($query) {
                            $query->selection()
                                ->where('status', 1)
                                ->where('is_active', 1);
                        });
                    });
                },
                'profile.badge',
                'profile.level',
                'profile.country'
            ])
            ->first();
        if (!$user) {
            // في حالة عدم وجود اسم مستخدم يتم إرسال رسالة الخطأ
            return response()->error(__("messages.errors.element_not_found"));
        } else {

            // في حالة وجود اسم المستخدم يتم عرض معلوماته الشخصية
            return response()->success(__("messages.oprations.get_data"), $user);
        }
    }

    public function show1($username, Request $request)
    {
        try{
        // البحث في قاعدة البيانات عن اسم المستخدم
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $user = User::where('username',$username)
            ->orWhere('id', $username)
            ->with([
                'profile.profile_seller'=>function($q) use($x_localization){
                    $q->select('id',"steps","number_of_sales","portfolio","bio_{$x_localization} AS bio",'profile_id', 'seller_badge_id','seller_level_id'
                ,'created_at');
                },
                'profile.profile_seller.badge'=>function($q) use($x_localization){
                    $q->select('id',"name_{$x_localization} AS name");
                },
                'profile.profile_seller.level' =>function($q) use($x_localization){
                    $q->select('id',"name_{$x_localization} AS name");
                },
                'profile.profile_seller.products'=>function($q) use($x_localization){
                    $q->select('id',"title_{$x_localization} AS title", "slug_{$x_localization} AS slug", "content_{$x_localization} AS content",
                     'price', 'duration', 'count_buying', 'thumbnail', "buyer_instruct_{$x_localization}",'status',
                      'profile_seller_id', 'ratings_avg', 'ratings_count', 'deleted_at')->where('status',1);
                },
                'profile' ,
                'profile.badge'=>function($q) use($x_localization){
                    $q->select('id',"name_{$x_localization} AS name");
                },
                'profile.level'=>function($q) use($x_localization){
                    $q->select('id',"name_{$x_localization} AS name", 'value_bayer_min', 'value_bayer_max','type', 'number_developments', 'price_developments', 'number_sales');
                },
                'profile.country'=>function($q) use($x_localization){
                    $q->select('id',"name_{$x_localization} AS name");
                }
            ])
            ->first();
        if (!$user) {
            // في حالة عدم وجود اسم مستخدم يتم إرسال رسالة الخطأ
            return response()->error(__("messages.errors.element_not_found"));
        } else {

            // في حالة وجود اسم المستخدم يتم عرض معلوماته الشخصية
            return response()->success(__("messages.oprations.get_data"), $user);
        }
    }
    catch (Exception $exc){
        echo $exc;
    }
}

    

    public function index(Request $request){
        try{
        $is_portfolio =!is_null($request->portfolio);
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
        $res = $is_portfolio ?  Profile::select('id', 'steps', 'first_name', 'last_name', 'full_name', 'steps', 'avatar', 'avatar_url', 'gender',
        'date_of_birth', 'precent_rating', 'user_id', 'country_id', 'badge_id', 'level_id', 'is_completed')
        
        ->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_detail'])
        ->with([
            'profile_seller'=>function($q) use($x_localization){
                $q->select('id', 'steps', 'number_of_sales', 'portfolio', "bio_{$x_localization} AS bio", 'profile_id')
                ->whereNotNull('portfolio');
                
            },
            'badge'=>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },
            'level' =>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },

        ])
        ->whereHas('profile_seller', function($q) {
            $q->whereNotNull('id');
        })
        ->paginate($paginate)
        :Profile::select('id', 'steps', 'first_name', 'last_name', 'full_name', 'steps', 'avatar', 'avatar_url', 'gender',
        'date_of_birth', 'precent_rating', 'user_id', 'country_id', 'badge_id', 'level_id', 'is_completed')
        ->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_detail'])
        ->with([
            'profile_seller'=>function($q) use($x_localization){
                $q->select('id', 'steps', 'number_of_sales', 'portfolio', "bio_{$x_localization} AS bio", 'profile_id')
                ->whereIn('profile_id');
            },
            'badge'=>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },
            'level' =>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },

        ])
        ->whereNotNull('profile_seller')
        ->paginate($paginate);
        if (!$res->isEmpty()) {
            return response()->success(__("messages.filter.filter_success"), $res);
        } else {
            return response()->success(__("messages.filter.filter_field"), [], 204);
        }
    }
    catch(Exception  $exc){
        echo $exc;
    }
}
    /**
     * step_one => دالة المرحلة الأولى في الملف الشخصي وهي مرحلة المعلومات الشخصية
     *
     * @param  ProfileStepOneRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */

    public function step_one(ProfileStepOneRequest $request)
    {
        try {
            $api_data = db::table('currencies')->join('api_currencies', 'currencies.code', '=', 'api_currencies.code')->select('currencies.*')
                ->get();
            $code_phones = Country::all()->groupBy('code_phone');
            if (is_null($code_phones[$request->code_phone])) {
                //throw new Exception("يجب إختيار كود هاتف متاح");
                return response()->error(__("messages.errors.element_not_found"), 404);
            }
            $user = Auth::user();
            // تغيير اسم المستخدم
            $user->username = $request->username;
            $user->phone = $request->phone;
            $user->code_phone = $request->code_phone;
            $user->save();

            // جلب الدولة
            $country = Country::with('currency')->where('id', $request->country_id)->first();
            // شرط اذا كانت الدولة موجودة
            if (is_null($country)) {
                return response()->error(__("messages.errors.element_not_found"), 404);
            }

            if (!is_null($request->currency_id)) {
                //echo "currency is there";
                $currency = Currency::where('id', $request->currency_id)->first();
                if (is_null($currency)) {
                    //return abort(404, 'تلك العملة غير موجودة');
                    return response()->error(__("messages.errors.element_not_found"), 404);
                }
                if ($api_data->where('code', $currency->code)->count() != 0) {
                    $user->profile->currency_id = $request->currency_id;
                }
            } else {
                if ($api_data->where('code', $country->currency->code)->count() != 0) {
                    $user->profile->currency_id = $country->currency_id;
                }
            }


            // تغيير المعلومات الشخصية

            $user->profile->first_name = $request->first_name;
            $user->profile->last_name = $request->last_name;
            $user->profile->full_name = $request->first_name . ' ' . $request->last_name;
            $user->profile->gender = $request->gender;
            $user->profile->date_of_birth = $request->date_of_birth;
            $user->profile->country_id = $request->country_id;
            $user->profile->lang = $request->has('lang') ? $request->lang:$country->lang;
            $user->profile->steps = Profile::COMPLETED_SETP_THREE;
            $user->profile->is_completed = true;
            $user->phone = $request->phone;
            $user->profile->save();
            // إرسال رسالة نجاح المرحلة اﻷولى
            return response()->success(__("messages.product.success_step_one"), $user);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }


    /**
     * step_two => دالة المرحلة الثانية في الملف الشخصي وهي مرحلة رفع الصورة الشخصية
     *
     * @param  ProfileStepTwoRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function step_two(ProfileStepTwoRequest $request)
    {
        try {

            // إنشاء اسم للصورة الشخصية
            $avatarPath = $request->file('avatar');
            $avatarName = 'tw-' . Auth::user()->id .  time() . '.' . $avatarPath->getClientOriginalExtension();
            // رفع الصورة
            $avatarPath->storePubliclyAs('avatars', $avatarName, 'do');
            //$path = Storage::putFileAs('avatars', $request->file('avatar'), $avatarName);
            // تخزين اسم الصورة في قاعدة البيانات
            $user = Auth::user();
            // تغيير اسم المستخدم

            $avatarUrl = 'https://timwoork-space.ams3.digitaloceanspaces.com/avatars/' . $avatarName;

            $user->profile->avatar = $avatarName;
            $user->profile->avatar_url = $avatarUrl;
            $user->profile->save();

            // إرسال رسالة نجاح المرحلة الثانية مع إرسال رابط الصورة كاملا
            return response()->success(__("messages.product.success_step_two"), $avatarUrl);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }

    /**
     * step_three => دالة المرحلة الثالثة في الملف الشخصي وهي مرحلة  معلومات الاتصال وفيها يخزّن رقم الهاتف
     *
     * @param  ProfileStepTwoRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function step_three(ProfileStepThreeRequest $request)
    {
        try {
            // إنشاء اسم للصورة الشخصية
            $user = Auth::user();
            $user->phone = $request->phone_number;
            $user->save();
            $user->profile->save();
            // إرسال رسالة نجاح المرحلة الثانية مع إرسال رابط الصورة كاملا
            return response()->success(__("messages.product.success_step_three"));
        } catch (Exception $ex) {
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }

    public function follow($id, Request $request){
        try{
        $profile = Auth::user()->profile;
        if ($id == $profile->id)
            return response()->error(400);
        $profile->following()->attach($id);
        return response()->success(__("messages.oprations.get_all_data"));
    }
    catch(Exception $exc){
        return response()->error(__("messages.errors.element_not_found"));
        
    }
}

    public function unfollow($id, Request $request){
        $profile = Auth::user()->profile;
        $profile->following()->detach($id);
        return response()->success(__("messages.oprations.get_all_data"));
    }
}
