<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SkillRequest;
use App\Models\Skill;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{

    /**
     * index => دالة عرض كل المهارات
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // جلب جميع المهارات عن طريق التصفح
        $skills = Skill::Selection()->get();
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $skills);
    }

    /**
     * store => دالة اضافة مهارة جديدة
     *
     * @param  SkillRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(SkillRequest $request): ?object
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'            => $request->name_ar,
                'name_en'            => $request->name_en,
                'name_fr'            => $request->name_fr,
            ];
            // ============= انشاء مهارة جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة مهارة :
            $skill = Skill::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $skill);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * show => id  دالة جلب مهارة معينة بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        //id  جلب العنصر بواسطة
        $skill = Skill::Selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود
        if (!$skill) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $skill);
    }


    /**
     * update => دالة تعديل على المهارة
     *
     * @param  mixed $id
     * @param  SkillRequest $request
     * @return object
     */
    public function update(SkillRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $skill = Skill::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$skill || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar' => $request->name_ar,
            ];
            //  في حالة ما اذا وجد الاسم بالانجليزية , اضفها الى مصفوفة التعديل:
            if ($request->name_en) {
                $data['name_en'] = $request->name_en;
            }
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل:
            if ($request->name_fr) {
                $data['name_fr'] = $request->name_fr;
            }
            // ============= التعديل على المهارة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على المهارة :
            $skill->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $skill);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => دالة حذف المهارة
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $skill = Skill::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$skill || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= حذف المهارة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف المهارة :
            $skill->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success(__("messages.oprations.delete_success"), $skill);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
