<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SellerLevelRequest;
use App\Models\SellerLevel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SellerLevelController extends Controller
{
    /**
     * index => دالة عرض كل المستويات
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /* جلب جميع المستويات البائعين عن طريق كيوري  ترسل من الفرونت اند
         من أجل عرض مستويات حسب نوعها
        type = 0
         مستويات خاصة بالمشتري
        type = 1
        مستويات خاصة بالبائع
        في حالة عدم إرسال الكيوري من الفرونت يتم عرض جميع المستويات
        */

        if ($request->query('type')) {
            $type = $request->query('type');
            $sellers_levels = SellerLevel::where('type', $type)->get();
        } else {
            $sellers_levels = SellerLevel::all();
        }
        return response()->success(__("messages.oprations.get_all_data"), $sellers_levels);
    }

    /**
     * store => دالة اضافة مستوى جديد
     *
     * @param  SellerLevelRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(SellerLevelRequest $request)
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'               => $request->name_ar,
                'name_en'               => $request->name_en,
                'name_fr'               => $request->name_fr,
                //'type'                  => $request->type,
                //'number_developments'   => $request->number_developments,
                //'price_developments'    => $request->price_developments,
                //'number_sales'          => $request->number_sales,
                //'value_bayer'          => $request->value_bayer,
            ];
            // ============= انشاء مستوى جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة مستوى :
            $seller_level = SellerLevel::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $seller_level);
        } catch (Exception $ex) {
            echo $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }


    /**
     * show => id  دالة جلب مستوى معين بواسطة المعرّف
     *
     *s @param  string $id => id متغير المعرف
     * @return object
     */
    public function show(mixed $id): ?object
    {
        //id  جلب العنصر بواسطة
        $seller_level = SellerLevel::Selection()->where('id', $id)->first();
        // شرط اذا كان العنصر موجود
        if (!$seller_level) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $seller_level);
    }

    /**
     * update => دالة تعديل على المستوى
     *
     * @param  mixed $id
     * @param  SellerLevelRequest $request
     * @return object
     */
    public function update(SellerLevelRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $seller_level = SellerLevel::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$seller_level || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'               => $request->name_ar,
                'name_en'               => $request->name_en,
                'name_fr'               => $request->name_fr,
                'type'                  => $request->type,
                'number_developments'   => $request->number_developments,
                'price_developments'    => $request->price_developments,
                'number_sales'          => $request->number_sales,
                'value_bayer'          => $request->value_bayer,
            ];

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على التصنيف :
            $seller_level->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $seller_level);
        } catch (Exception $ex) {
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => دالة حذف المستوى
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $seller_level = SellerLevel::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$seller_level || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $seller_level->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success(__('messages.oprations.delete_success'));
        } catch (Exception $ex) {
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
