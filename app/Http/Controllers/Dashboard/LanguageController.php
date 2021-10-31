<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LanguageRequest;
use App\Models\Language;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * index => دالة عرض كل اللغات
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // جلب جميع اللغة عن طريق التصفح
        $languages = Language::Selection()->get();
        // اظهار العناصر
        return response()->success('تم العثور على قائمة اللغات', $languages);
    }

    /**
     * store => دالة اضافة لغة جديدة
     *
     * @param  LanguageRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(LanguageRequest $request): ?object
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'            => $request->name_ar,
                'name_en'            => $request->name_en,
                'name_fr'            => $request->name_fr,
            ];
            // ============= انشاء لغة جديدة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة لغة :
            $language = Language::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء لغة جديدة بنجاح', $language);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * show => id  دالة جلب لغة معين بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف 
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        //id  جلب العنصر بواسطة
        $language = Language::Selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود
        if (!$language)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العنصر
        return response()->success('تم جلب العنصر بنجاح', $language);
    }


    /**
     * update => دالة تعديل على اللغة
     *
     * @param  mixed $id
     * @param  LanguageRequest $request
     * @return object
     */
    public function update(LanguageRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف 
            $language = Language::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$language || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar' => $request->name_ar,
            ];
            //  في حالة ما اذا وجد الاسم بالانجليزية , اضفها الى مصفوفة التعديل: 
            if ($request->name_en)
                $data['name_en'] = $request->name_en;
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->name_fr)
                $data['name_fr'] = $request->name_fr;
            // ============= التعديل على اللغة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على اللغة :
            $language->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success('تم التعديل على اللغة بنجاح', $language);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * delete => دالة حذف اللغة
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف 
            $language = Language::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$language || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // ============= حذف اللغة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف اللغة :
            $language->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success('تم حذف اللغة بنجاح', $language);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // return $ex;
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
