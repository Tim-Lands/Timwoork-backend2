<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSellerStoreRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ProfileSellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user']);
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

    public function bio(ProfileSellerStoreRequest $request)
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

}
