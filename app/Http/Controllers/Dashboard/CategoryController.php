<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{


    /**
     * index => دالة عرض كل التصنيفات
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // جلب جميع الاصناف عن طريق التصفح
        $categories = Category::Selection()->whereParentId(null)->paginate(Category::PAGINATE);
        return response()->json(['message' => 'عرض كل التصنيفات', $categories], 200);
    }


    /**
     * show => slug  دالة جلب تصنيف معين بواسطة سلاق
     *
     *s @param  string $slug => slug متغير المعرف 
     * @return object
     */
    public function show(string $slug): ?object
    {
        //slug  جلب العنصر بواسطة
        $category = Category::Selection()->whereSlug($slug)->first();
        // شرط اذا كان العنصر موجود
        if (!$category)
            // رسالة خطأ
            return response()->json('هذا العنصر غير موجود', 403);

        // اظهار العنصر
        return response()->json($category, 200);
    }

    /**
     * store => دالة اضافة تصنيف جديد
     *
     * @param  CategoryRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(CategoryRequest $request)
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'        => $request->name_ar,
                'name_en'        => $request->name_en,
                'name_fr'        => $request->name_fr,
                'slug'           => Str::slug($request->name_en),
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
                'icon'           => $request->icon
            ];
            // ============= انشاء تصنيف جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة تصنيف :
            Category::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->json('تم انشاء تصنيف جديد بنجاح', 201);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
            return response()->json('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 400);
        }
    }

    /**
     * update => دالة تعديل على التصنيف
     *
     * @param  mixed $id
     * @param  CategoryRequest $request
     * @return object
     */
    public function update(mixed $id, CategoryRequest $request): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف 
            $category = Category::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$category || !is_numeric($id))
                // رسالة خطأ
                return response()->json('هذا العنصر غير موجود', 403);

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'        => $request->name_ar,
                'name_en'        => $request->name_en,
                'slug'           => Str::slug($request->name_en),
                'icon'           => $request->icon
            ];
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->name_fr)
                $data['name_fr'] = $request->name_fr;
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->description_ar)
                $data['description_ar'] = $request->description_ar;
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->description_en)
                $data['description_en'] = $request->description_en;
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل: 
            if ($request->description_fr)
                $data['description_fr'] = $request->description_fr;

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على التصنيف :
            $category->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->json('تم التعديل على تصنيف بنجاح', 201);
        } catch (Exception $ex) {
            // رسالة خطأ :
            return response()->json('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 400);
        }
    }

    /**
     * delete => دالة حذف التصنيف
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف 
            $category = Category::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$category || !is_numeric($id))
                // رسالة خطأ
                return response()->json('هذا العنصر غير موجود', 403);
            // جلب عدد التصنيفات الفرعية
            $subcategory = $category->whereNotNull('parent_id')->count();

            // شرط اذا كان العنصر لديه تصنيفات فرعية ام لا
            if ($subcategory > 0)
                // رسالة خطأ
                return response()->json('لا تستطيع حذف هذا العنصر بسبب علاقته مع العناصر الفرعية', 403);

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $category->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->json('تم حذف تصنيف بنجاح', 201);
        } catch (Exception $ex) {
            // return $ex;
            return response()->json('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 400);
        }
    }
}
