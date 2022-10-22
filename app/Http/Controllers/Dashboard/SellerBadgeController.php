<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SellerBadgeRequest;
use App\Models\SellerBadge;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SellerBadgeController extends Controller
{

    /**
     * index => دالة عرض كل الشارات
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
        // جلب جميع الاصناف عن طريق التصفح
        $badges = SellerBadge::select('id',"name_{$xlocalization} AS name", 'precent_deducation')->get();
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $badges);
    }

    /**
     * store => دالة اضافة شارة جديدة
     *
     * @param  SellerBadgeRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(SellerBadgeRequest $request): ?object
    {
        try {
            $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'            => $request->name_ar,
                'name_en'            => $request->name_en,
                'name_fr'            => $request->name_fr,
                'precent_deducation' => $request->precent_deducation
            ];
            // ============= انشاء شارة جديدة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة شارة :
            $seller_badge = SellerBadge::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $name_localization = "name_{$xlocalization}";
            $seller_badge = (object)$seller_badge;
            $seller_badge->name = $seller_badge->$name_localization;
            unset($seller_badge->name_ar, $seller_badge->name_en, $seller_badge->name_fr);
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__('messages.oprations.add_success'), $seller_badge);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * show => id  دالة جلب الشارة معين بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        //slug  جلب العنصر بواسطة
        $seller_badge = SellerBadge::Selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود
        if (!$seller_badge) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }

        // اظهار العنصر
        return response()->success(__("messages.oprations.update_success"), $seller_badge);
    }


    /**
     * update => دالة تعديل على الشارة
     *
     * @param  mixed $id
     * @param  SellerBadgeRequest $request
     * @return object
     */
    public function update(SellerBadgeRequest $request, mixed $id): ?object
    {
        try {
            $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $seller_badge = SellerBadge::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$seller_badge || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'            => $request->name_ar,
                'precent_deducation' => $request->precent_deducation
            ];
            //  في حالة ما اذا وجد الاسم بالانجليزية , اضفها الى مصفوفة التعديل:
            if ($request->name_en) {
                $data['name_en'] = $request->name_en;
            }
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل:
            if ($request->name_fr) {
                $data['name_fr'] = $request->name_fr;
            }
            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على التصنيف :
            $seller_badge->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $name_localization = "name_{$xlocalization}";
            $seller_badge = (object)$seller_badge;
            $seller_badge->name = $seller_badge->$name_localization;
            unset($seller_badge->name_ar, $seller_badge->name_en, $seller_badge->name_fr);
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $seller_badge);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => دالة حذف الشارة
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $seller_badge = SellerBadge::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$seller_badge || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= حذف الشارة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الشارة :
            $seller_badge->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success(__("messages.oprations.delete_success"), $seller_badge);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة الخطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
