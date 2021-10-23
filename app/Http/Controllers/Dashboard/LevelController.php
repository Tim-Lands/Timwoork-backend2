<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LevelRequest;
use App\Models\Level;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        return response()->success('لقد تمّ جلب المستويات بنجاح', $levels);
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
            return response()->success('تم إضافة المستوى بنجاح', $level);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            //return $ex;
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك');
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
        if (!$level)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العنصر
        return response()->success('لقد تم العثور على  بنجاح', $level);
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
            if (!$level || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

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
            return response()->success('لقد تم التعديل على المستوى بنجاح', $level);
        } catch (Exception $ex) {
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 400);
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
            if (!$level || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $level->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success('تم حذف المستوى بنجاح');
        } catch (Exception $ex) {
            // return $ex;
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 400);
        }
    }
}
