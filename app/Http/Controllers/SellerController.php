<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileSellerStoreRequest;
use App\Http\Requests\SellerStepOneRequest;
use App\Http\Requests\SellerSteptwoRequest;
use App\Models\ProfileSeller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * store => دالة إنشاء ملف شخصي فارغ للبائع
     *
     * @return object
     */
    public function store()
    {
        try {
            if (!Auth::user()->profile->is_completed) {
                return response()->error(__("messages.product.profile_not_complete"), 422);
            }
            // إنشاء ملف شخصي للبائع
            $seller = Auth::user()->profile->profile_seller()->create([
                'bio' => '',
                'portfolio' => '',
                'seller_badge_id' => 1,
                'seller_level_id' => 1,
            ]);
            return response()->success(__("messages.oprations.add_success"), $seller);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }


    /**
     * store => دالة إنشاء ملف شخصي فارغ للبائع
     *
     * @param  Request $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function detailsStore(ProfileSellerStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $seller = Auth::user()->profile->profile_seller;
            $seller->bio = $request->bio;
            $seller->portfolio = $request->portfolio;
            $seller->save();
            // تسجيل المهارات الخاصة للبائع
            /*             $skills = [];
                        // تهيئة المهارات بوضعها في مصفوفة
                        for ($i = 0; $i < count($request->skills); $i++) {
                            $skills[$request->skills[$i]["id"]]  = ["level" => $request->skills[$i]["level"]];
                        }
                        $seller->skills()->syncWithoutDetaching($skills); */
            // تغيير حالة البروفايل إلى بائع
            $seller->profile->is_seller = true;

            $seller->profile->save();
            DB::commit();

            // إرسال رسالة نجاح
            return response()->success(__("messages.seller.add_profile_seller"), $seller);
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }

    /**
     * step_one => دالة المرحلة الأولى في الملف الشخصي وهي مرحلة المعلومات الشخصية
     *
     * @param  ProfileStepOneRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */

    public function step_one(SellerStepOneRequest $request)
    {
        try {
            $seller = Auth::user()->profile->profile_seller;
            $seller->bio = $request->bio;
            $seller->portfolio = $request->portfolio;
            $seller->languages()->syncWithoutDetaching($request->languages);
            $seller->steps = ProfileSeller::COMPLETED_SETP_ONE;
            $seller->save();
            // إرسال رسالة نجاح المرحلة اﻷولى
            return response()->success(__("messages.product.success_step_one"), $seller);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }


    /**
     * step_one => دالة المرحلة الثانية في الملف الشخصي للبائع وهي مرحلة المعلومات الوظيفية
     *
     * @param  SellerSteptwoRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */

    public function step_two(SellerSteptwoRequest $request)
    {
        try {
            $seller = Auth::user()->profile->profile_seller;
            $seller->professions()->syncWithoutDetaching($request->professions);
            $seller->skills()->syncWithoutDetaching($request->skills);
            $seller->steps = ProfileSeller::COMPLETED_SETP_TWO;
            $seller->save();
            // إرسال رسالة نجاح المرحلة اﻷولى
            return response()->success(__("messages.product.success_step_two"), $seller);
        } catch (Exception $ex) {
            return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }
}
