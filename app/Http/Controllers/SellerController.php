<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileSellerStoreRequest;
use App\Http\Requests\SellerStepOneRequest;
use App\Http\Requests\SellerSteptwoRequest;
use App\Models\ProfileSeller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class SellerController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user']);
    }

    /**
     * store => دالة إنشاء ملف شخصي فارغ للبائع
     *
     * @return object
     */

    public function index(Request $request){
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $profile = Auth::user()->profile->profile_seller;
        $profile_json = (object) $profile;
        $bio_localization = "bio_{$x_localization}";
        $name_localization = "name_{$x_localization}";
        $profile_json->bio = $profile->$bio_localization;
        $profile_json->level->name = $profile_json->level->$name_localization;
        $profile_json->badge->name = $profile_json->badge->$name_localization;
        unset($profile_json->bio_ar, $profile_json->bio_en, $profile_json->bio_fr,$profile_json->level->name_ar,
         $profile_json->level->name_en, $profile_json->level->name_fr,
         $profile_json->badge->name_ar,$profile_json->badge->name_en,$profile_json->badge->name_fr);
        return $profile_json;
    }

    public function store()
    {
        try {
            if (!Auth::user()->profile->is_completed) {
                return response()->error(__("messages.product.profile_not_complete"), 422);
            }
            // إنشاء ملف شخصي للبائع
            $seller = Auth::user()->profile->profile_seller()->create([
                'bio' => '',
                'bio_fr' => '',
                'bio_en' => '',
                'bio_ar' => '',
                'portfolio' => '',
                'seller_badge_id' => 1,
                'seller_level_id' => 1,
            ]);
            $profile = Auth::user()->profile;
            $profile->is_seller = 1;
            $profile->save();
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
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            $xlocalization = "ar";
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->bio);
            $xlocalization = $tr->getLastDetectedSource();

            $tr->setSource($xlocalization);
            $bio_ar = $request->bio_ar;
            $bio_en = $request->bio_en;
            $bio_fr = $request->bio_fr;

            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            switch ($xlocalization) {
                case "ar":
                    if (is_null($bio_en)) {
                        $tr->setTarget('en');
                        $bio_en = $tr->translate($request->bio);
                    }
                    if (is_null($bio_fr)) {
                        $tr->setTarget('fr');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_ar = $request->bio;
                    break;
                case 'en':
                    if (is_null($bio_ar)) {
                        $tr->setTarget('ar');
                        $bio_ar = $tr->translate($request->bio);
                    }
                    if (is_null($bio_fr)) {
                        $tr->setTarget('fr');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_en = $request->bio;
                    break;
                case 'fr':
                    if (is_null($bio_en)) {
                        $tr->setTarget('en');
                        $bio_en = $tr->translate($request->bio);
                    }
                    if (is_null($bio_ar)) {
                        $tr->setTarget('ar');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_fr = $request->bio;
                    break;
            }

            DB::beginTransaction();
            $seller = Auth::user()->profile->profile_seller;
            $seller->bio = $request->bio;
            $seller->bio_en = $bio_en;
            $seller->bio_fr = $bio_fr;
            $seller->bio_ar = $bio_ar;
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
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            else {
                $tr->setSource();
                $tr->setTarget('en');
                $tr->translate($request->bio);
                $xlocalization = $tr->getLastDetectedSource();
            }
            $tr->setSource($xlocalization);
            $bio_ar = $request->bio_ar;
            $bio_en = $request->bio_en;
            $bio_fr = $request->bio_fr;

            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            switch ($xlocalization) {
                case "ar":
                    if (is_null($bio_en)) {
                        $tr->setTarget('en');
                        $bio_en = $tr->translate($request->bio);
                    }
                    if (is_null($bio_fr)) {
                        $tr->setTarget('fr');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_ar = $request->bio;
                    break;
                case 'en':
                    if (is_null($bio_ar)) {
                        $tr->setTarget('ar');
                        $bio_ar = $tr->translate($request->bio);
                    }
                    if (is_null($bio_fr)) {
                        $tr->setTarget('fr');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_en = $request->bio;
                    break;
                case 'fr':
                    if (is_null($bio_en)) {
                        $tr->setTarget('en');
                        $bio_en = $tr->translate($request->bio);
                    }
                    if (is_null($bio_ar)) {
                        $tr->setTarget('ar');
                        $bio_fr = $tr->translate($request->bio);
                    }
                    $bio_fr = $request->bio;
                    break;
            }



            $seller = Auth::user()->profile->profile_seller;
            $seller->bio = $request->bio;
            $seller->bio_en = $bio_en;
            $seller->bio_fr = $bio_fr;
            $seller->bio_ar = $bio_ar;
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
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }
}
