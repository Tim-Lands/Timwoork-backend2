<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Models\Country;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * index => دالة عرض كل الدول
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // جلب جميع الدول عن طريق التصفح
        $countries = Country::Selection()->get();
        // اظهار العناصر
        return response()->success('تم العثور على قائمة الدول', $countries);
    }

    /**
     * store => دالة اضافة دولة جديد
     *
     * @param  CountryRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(CountryRequest $request): ?object
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'            => $request->name_ar,
                'name_en'            => $request->name_en,
                'name_fr'            => $request->name_fr,
                'code_phone'         => $request->code_phone
            ];

            // ============= انشاء دولة جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة دولة :
            $country = Country::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء دولة جديد بنجاح', $country);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * show => id  دالة جلب دولة معين بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف 
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        //id  جلب العنصر بواسطة
        $country = Country::Selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود
        if (!$country)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العنصر
        return response()->success('تم جلب العنصر بنجاح', $country);
    }


    /**
     * update => دالة تعديل على الدولة
     *
     * @param  mixed $id
     * @param  CountryRequest $request
     * @return object
     */
    public function update(CountryRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف 
            $country = Country::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$country || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'    => $request->name_ar,
                'code_phone' => $request->code_phone
            ];
            //  في حالة ما اذا وجد الاسم بالانجليزية , اضفها الى مصفوفة التعديل: 
            if ($request->name_en)
                $data['name_en'] = $request->name_en;
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->name_fr)
                $data['name_fr'] = $request->name_fr;
            // ============= التعديل على الدولة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على الدولة :
            $country->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success('تم التعديل على الدولة بنجاح', $country);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * delete => دالة حذف الدولة
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف 
            $country = Country::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$country || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // ============= حذف الدولة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الدولة :
            $country->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success('تم حذف الدولة بنجاح', $country);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // return $ex;
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
