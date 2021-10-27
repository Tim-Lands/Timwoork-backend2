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
        // جلب جميع الاصناف الرئيسة و الاصناف الفرعية عن طريق التصفح
        $categories = Category::Selection()->with(['subcategories' => function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'parent_id', 'icon');
        }])->whereParentId(null)->get();

        // اظهار العناصر
        return response()->success('عرض كل تصنيفات الرئيسية و الفرعية', $categories);
    }


    /**
     * show => id  دالة جلب تصنيف معين بواسطة سلاق
     *
     *s @param  mixed $id => id متغير المعرف 
     * @return object
     */
    public function show(mixed $id): ?object
    {
        //id  جلب العنصر بواسطة
        $category = Category::selection()->whereId($id)->with(['subcategories' => function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'parent_id', 'icon');
        }])->first();
        // شرط اذا كان العنصر موجود
        if (!$category)
            //رسالة خطأ    
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العنصر
        return response()->success('تم جلب العنصر بنجاح', $category);
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
            $category = Category::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء تصنيف جديد بنجاح', $category);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
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
                //رسالة خطأ    
                return response()->error('هذا العنصر غير موجود', 403);

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
            //  في حالة ما اذا وجد الوصف بالعربية , اضفها الى مصفوفة التعديل: 
            if ($request->description_ar)
                $data['description_ar'] = $request->description_ar;
            //  في حالة ما اذا وجد الوصف بالانجليزية , اضفها الى مصفوفة التعديل: 
            if ($request->description_en)
                $data['description_en'] = $request->description_en;
            //  في حالة ما اذا وجد الاسم بالفرنسية , اضفها الى مصفوفة التعديل: 
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
            return response()->success('تم التعديل على تصنيف بنجاح', $category);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
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
            $category = Category::selection()->whereId($id)->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$category || !is_numeric($id))
                //رسالة خطأ    
                return response()->error('هذا العنصر غير موجود', 403);
            // جلب عدد التصنيفات الفرعية
            $subcategory = $category->whereId($id)->withCount('subCategories')->first()->sub_categories_count;

            // شرط اذا كان العنصر لديه تصنيفات فرعية ام لا
            if ($subcategory > 0)
                // رسالة خطأ    
                return response()->error('لا تستطيع حذف هذا العنصر بسبب علاقته مع العناصر الفرعية', 403);

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $category->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success('تم حذف تصنيف بنجاح', $category);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
