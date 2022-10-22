<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{


    /**
     * index => دالة عرض كل التصنيفات
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
        // جلب جميع الاصناف الرئيسة و الاصناف الفرعية عن طريق التصفح
        $categories = Category::select('id',"name_{$xlocalization} AS name", "slug", "description_{$xlocalization} AS description","icon","parent_id",'image')->with(['subcategories' => function ($q) use($xlocalization) {
            $q->select('id', "name_{$xlocalization} AS name", 'parent_id', 'icon');
        }])->parent()->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $categories);
    }
    /**
     * show => id  دالة جلب تصنيف معين بواسطة سلاق
     *
     *s @param  mixed $id => id متغير المعرف
     * @return object
     */
    public function show(mixed $id, Request $request): ?object
    {
        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
        //id  جلب العنصر بواسطة
        $category = Category::select("id", "name_{$xlocalization} AS name", 'slug', "description_{$xlocalization} AS description", "icon", "image")->whereId($id)->with(['subcategories' => function ($q) use($xlocalization) {
            $q->select('id', "name_{$xlocalization} AS name", 'parent_id', 'icon');
        }])->first();
        // شرط اذا كان العنصر موجود
        if (!$category) {
            //رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $category);
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
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
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
            $name_localization="name_{$xlocalization}";
            $description_localization = "description_{$xlocalization}";
            $category_json = (object)$category;
            $category_json->name = $category->$name_localization;
            $category_json->description = $category->$description_localization;
            unset($category_json->name_ar, $category_json->name_en, $category_json->name_fr, $category_json->description_ar, $category_json->description_en, $category_json->description_fr);
            return response()->success(__("messages.oprations.add_success"), $category_json);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), 403);
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
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $category = Category::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$category || !is_numeric($id)) {
                //رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'        => $request->name_ar,
                'name_en'        => $request->name_en,
                'slug'           => Str::slug($request->name_en),
                'icon'           => $request->icon
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
            $category->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            $name_localization="name_{$xlocalization}";
            $description_localization = "description_{$xlocalization}";
            $category_json = (object)$category;
            $category_json->name = $category->$name_localization;
            $category_json->description = $category->$description_localization;
            unset($category_json->name_ar, $category_json->name_en, $category_json->name_fr, $category_json->description_ar, $category_json->description_en, $category_json->description_fr);
            // =================================================
            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $category_json);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
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
            $category = Category::selection()->whereId($id)->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$category || !is_numeric($id)) {
                //رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $category->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.delete_success"), $category);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
