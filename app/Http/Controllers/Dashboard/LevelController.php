<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LevelRequest;
use App\Models\Level;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{

    /**
     * index => دالة عرض كل المستويات
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /* جلب جميع المستويات عن طريق كيوري  ترسل من الفرونت اند
         من أجل عرض مستويات حسب نوعها
        type = 0
         مستويات خاصة بالمشتري
        type = 1
        مستويات خاصة بالبائع
        في حالة عدم إرسال الكيوري من الفرونت يتم عرض جميع المستويات
        */

        if ($request->query('type')) {
            $type = $request->query('type');
            $levels = Level::where('type', $type)->get();
        } else {
            $levels = Level::all();
        }
        return response()->success(__("messages.oprations.get_all_data"), $levels);
    }

    public function index1(Request $request): JsonResponse
    {
        /* جلب جميع المستويات عن طريق كيوري  ترسل من الفرونت اند
         من أجل عرض مستويات حسب نوعها
        type = 0
         مستويات خاصة بالمشتري
        type = 1
        مستويات خاصة بالبائع
        في حالة عدم إرسال الكيوري من الفرونت يتم عرض جميع المستويات
        */
        try{
        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');

        if ($request->query('type')) {
            $type = $request->query('type');
            $levels = Level::where('type', $type)->select('id',"name_{$xlocalization} AS name")->get(); 
        } else {
            $levels = Level::select('id',"name_{$xlocalization} AS name")->get();
        }
        return response()->success(__("messages.oprations.get_all_data"), $levels);
    }
    catch(Exception $ex){
        echo $ex;
    }
    }

    /**
     * store => دالة اضافة مستوى جديد
     *
     * @param  LevelRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(LevelRequest $request)
    {
        try {
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
            // ============= انشاء مستوى جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة مستوى :
            $level = Level::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $level);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }

    public function store1(LevelRequest $request)
    {
        try {
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'               => $request->name_ar,
                'name_en'               => $request->name_en,
                'name_fr'               => $request->name_fr,
                'type'                  => $request->type,
                'number_developments'   => $request->number_developments,
                'price_developments'    => $request->price_developments,
                'number_sales'          => $request->number_sales,
                'value_bayer_max'          => $request->value_bayer,
            ];
            // ============= انشاء مستوى جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة مستوى :
            $level = Level::create($data);
            // انهاء المعاملة بشكل جيد :
            #DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            $name_localization = "name_{$xlocalization}";
            $level = (object) $level;
            $level->name = $level->$name_localization;
            unset($level->name_ar, $level->name_en, $level->name_fr);
            return response()->success(__("messages.oprations.add_success"), $level);
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
        $level = Level::Selection()->where('id', $id)->first();
        // شرط اذا كان العنصر موجود
        if (!$level) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $level);
    }

    public function show1(mixed $id, Request $request): ?object
    {
        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
        //id  جلب العنصر بواسطة
        $level = Level::select('id',"name_{$xlocalization} AS name")->where('id', $id)->first();
        // شرط اذا كان العنصر موجود
        if (!$level) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $level);
    }

    /**
     * update => دالة تعديل على المستوى
     *
     * @param  mixed $id
     * @param  LevelRequest $request
     * @return object
     */
    public function update(LevelRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $level = Level::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$level || !is_numeric($id)) {
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
            $level->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $level);
        } catch (Exception $ex) {
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    public function update1(LevelRequest $request, mixed $id): ?object
    {
        try {
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $level = Level::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$level || !is_numeric($id)) {
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
                'value_bayer_max'          => $request->value_bayer,
            ];

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على التصنيف :
            $level->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $name_localization = "name_{$xlocalization}";
            $level = (object) $level;
            $level->name = $level->$name_localization;
            unset($level->name_ar, $level->name_en, $level->name_fr);
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $level);
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
            $level = Level::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$level || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $level->delete();
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

    public function delete1(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $level = Level::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$level || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $level->delete();
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
