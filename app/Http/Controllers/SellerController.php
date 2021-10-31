<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerStepOneRequest;
use App\Http\Requests\SellerSteptwoRequest;
use App\Models\ProfileSeller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    /**
     * store => دالة إنشاء ملف شخصي فارغ للبائع
     *
     * @param  Request $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(Request $request)
    {
        try {
            // إنشاء ملف شخصي للبائع 
            $seller = Auth::user()->profile->profile_seller()->create();
            return response()->success('تمّ إنشاء الملف الشخصي للبائع بنجاح', $seller);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error('حدث خطأ غير متوقع');
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
            return response()->success('نجاح المرحلة اﻷولى', $seller);
        } catch (Exception $ex) {
            //return $ex;
            return response()->error('حدث خطأ غير متوقع');
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
            return response()->success('نجاح المرحلة الثانية', $seller);
        } catch (Exception $ex) {
            return $ex;
            return response()->error('حدث خطأ غير متوقع');
        }
    }
}
