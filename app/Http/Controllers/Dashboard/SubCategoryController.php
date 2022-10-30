<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SubCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{

    /**
     * create => دالة عرض تصنيفات الرئيسية من اجل الانشاء تصنيف الفرعي
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        // جلب التصنيفات الرئيسية
        $categories = Category::selection()->parent()->pluck('name_ar', 'id');
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $categories);
    }

    public function create1(): JsonResponse
    {
        // جلب التصنيفات الرئيسية
        $categories = Category::selection()->parent()->pluck('name_ar', 'id');
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $categories);
    }
    /**
     * show => id  دالة جلب تصنيف فرعي معين بواسطة المعرف
     *
     *s @param  mixed $id => id متغير المعرف
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {

        //id  جلب العنصر بواسطة
        $subcategory = Category::selection()->whereId($id)->child()->first();
        // شرط اذا كان العنصر موجود ام لا
        if (!$subcategory) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $subcategory);
    }

    public function show1(mixed $id, Request $request): JsonResponse
    {

        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
        //id  جلب العنصر بواسطة
        $subcategory = Category::select('id', "name_{$xlocalization} AS name", 'slug', "description_{$xlocalization} AS description",'icon', 'parent_id')->whereId($id)->child()->first();
        // شرط اذا كان العنصر موجود ام لا
        if (!$subcategory) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $subcategory);
    }


    /**
     * store => دالة اضافة تصنيف فرعي جديد
     *
     * @param  SubCategoryRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(SubCategoryRequest $request): ?object
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
                'parent_id'      => $request->id,
                'icon'           => $request->icon
            ];
            // ============= انشاء تصنيف جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة تصنيف :
            $subcategory = Category::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $subcategory);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    public function store1(SubCategoryRequest $request): ?object
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
                'parent_id'      => $request->parent_id,
                'icon'           => $request->icon
            ];
            // ============= انشاء تصنيف جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة تصنيف :
            $subcategory = Category::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $subcategory);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * update => دالة تعديل على التصنيف
     *
     * @param  mixed $id
     * @param  SubCategoryRequest $request
     * @return object
     */
    public function update(mixed $id, SubCategoryRequest $request): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $subcategory = Category::selection()->whereId($id)->child()->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$subcategory || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'        => $request->name_ar,
                'name_en'        => $request->name_en,
                'slug'           => Str::slug($request->name_en),
                'icon'           => $request->icon,
                'parent_id'      => $request->parent_id
            ];
            //  في حالة ما اذا وجد الاسم بالفرنيسة , اضفها الى مصفوفة التعديل:
            if ($request->name_fr) {
                $data['name_fr'] = $request->name_fr;
            }
            //  في حالة ما اذا وجد الوصف بالعربية , اضفها الى مصفوفة التعديل:
            if ($request->description_ar) {
                $data['description_ar'] = $request->description_ar;
            }
            //  في حالة ما اذا وجد الوصف بالانجليزية , اضفها الى مصفوفة التعديل:
            if ($request->description_en) {
                $data['description_en'] = $request->description_en;
            }
            //  في حالة ما اذا وجد الاسم بالفرنسية , اضفها الى مصفوفة التعديل:
            if ($request->description_fr) {
                $data['description_fr'] = $request->description_fr;
            }

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على التصنيف :
            $subcategory->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $subcategory);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
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
            $subcategory = Category::selection()->whereId($id)->child()->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$subcategory || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= حذف التصنيف الفرعي  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف الفرعي:
            $subcategory->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.delete_success"), $subcategory);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
