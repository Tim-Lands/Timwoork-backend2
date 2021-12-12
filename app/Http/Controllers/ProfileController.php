<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStepOneRequest;
use App\Http\Requests\ProfileStepThreeRequest;
use App\Http\Requests\ProfileStepTwoRequest;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function show($username)
    {
        // البحث في قاعدة البيانات عن اسم المستخدم
        $user = User::where('username', $username)
            ->orWhere('id', $username)
            ->with([
                'profile.profile_seller.badge',
                'profile.profile_seller.level',
                'profile.badge', 'profile.level'
            ])
            ->first();
        if (!$user) {
            // في حالة عدم وجود اسم مستخدم يتم إرسال رسالة الخطأ
            return response()->error('عذرا لم نجد معلومات مطابقة لهذا الاسم');
        } else {
            // make some columns hidden in response
            $user->makeHidden('created_at', 'updated_at', 'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at');
            $user->profile->makeHidden(['steps', 'user_id', 'badge_id', 'level_id', 'country_id', 'created_at', 'updated_at']);
            $user->profile->badge->makeHidden(['name_en', 'name_fr', 'created_at', 'updated_at']);
            $user->profile->level->makeHidden(['name_en', 'name_fr', 'number_developments', 'price_developments', 'number_sales', 'created_at', 'updated_at']);
            $user->profile->profile_seller->makeHidden(['steps', 'badge_id', 'level_id', 'profile_id', 'created_at', 'updated_at']);
            $user->profile->profile_seller->skills->makeHidden(['name_en', 'name_fr', 'pivot', 'created_at', 'updated_at']);
            $user->profile->profile_seller->badge->makeHidden(['name_en', 'name_fr', 'created_at', 'updated_at']);
            $user->profile->profile_seller->level->makeHidden(['name_en', 'name_fr', 'value_bayer', 'created_at', 'updated_at']);

            // في حالة وجود اسم المستخدم يتم عرض معلوماته الشخصية
            return response()->success('لقد تمّ جلب معلومات الملف الشخصي', $user);
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

            $user = Auth::user();
            // تغيير اسم المستخدم 
            $user->username = $request->username;
            $user->save();
            // تغيير المعلومات الشخصية 
            $user->profile->first_name = $request->first_name;
            $user->profile->last_name = $request->last_name;
            $user->profile->gender = $request->gender;
            $user->profile->date_of_birth = $request->date_of_birth;
            $user->profile->country_id = $request->country_id;
            $user->profile->steps = Profile::COMPLETED_SETP_ONE;
            $user->profile->save();
            // إرسال رسالة نجاح المرحلة اﻷولى
            return response()->success('نجاح المرحلة اﻷولى', $user);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error('حدث خطأ غير متوقع');
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
            $path = Storage::putFileAs('avatars', $request->file('avatar'), $avatarName);
            // تخزين اسم الصورة في قاعدة البيانات
            $user = Auth::user();
            // تغيير اسم المستخدم 
            $user->profile->avatar = $avatarName;
            $user->profile->steps = Profile::COMPLETED_SETP_TWO;
            $user->profile->save();
            // إرسال رسالة نجاح المرحلة الثانية مع إرسال رابط الصورة كاملا
            return response()->success('نجاح المرحلة اﻷولى', Storage::disk('avatars')->url($avatarName));
        } catch (Exception $ex) {
            //return $ex;
            return response()->error('حدث خطأ غير متوقع');
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
            $user->profile->steps = Profile::COMPLETED_SETP_THREE;
            $user->profile->save();
            // إرسال رسالة نجاح المرحلة الثانية مع إرسال رابط الصورة كاملا
            return response()->success('نجاح المرحلة الثالثة');
        } catch (Exception $ex) {
            //return $ex;
            return response()->error('حدث خطأ غير متوقع');
        }
    }
}
