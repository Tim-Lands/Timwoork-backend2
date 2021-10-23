<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SubCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
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
        $categories = Category::selection()->whereNull('parent_id')->pluck('name_ar', 'id');
        // اظهار العنصر
        return response()->json(
            [
                'status' => true,
                'الرسالة' => 'عرض التصنيفات الرئيسية من اجل انشاء',
                'data'   => $categories
            ],
            200
        );
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
        $subcategory = Category::selection()->whereId($id)->whereNotNull('parent_id')->first();
        // شرط اذا كان العنصر موجود ام لا
        if (!$subcategory)
            return response()->json(
                [
                    'الرسالة' => 'هذا العنصر غير موجود',
                    'status' => false,
                ],
                403
            );

        // اظهار العنصر
        return response()->json(
            [
                'status' => true,
                'data'   => $subcategory
            ],
            200
        );
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
            return response()->json(
                [
                    'status' => true,
                    'الرسالة' => 'تم انشاء تصنيف فرعي جديد بنجاح',
                    'data' => $subcategory
                ],
                201
            );
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->json(
                [
                    'status' => false,
                    'الرسالة' => 'هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك',
                ],
                403
            );
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
            $subcategory = Category::selection()->whereId($id)->whereNotNull('parent_id')->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$subcategory || !is_numeric($id))
                // رسالة خطأ
                return response()->json(
                    [
                        'الرسالة' => 'هذا العنصر غير موجود',
                        'status' => false,
                    ],
                    403
                );

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'        => $request->name_ar,
                'name_en'        => $request->name_en,
                'slug'           => Str::slug($request->name_en),
                'icon'           => $request->icon,
                'parent_id'      => $request->parent_id
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
            $subcategory->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الاضافة:
            return response()->json(
                [
                    'status' => true,
                    'الرسالة' => 'تم التعديل على تصنيف الفرعي بنجاح',
                    'data' => $subcategory
                ],
                201
            );
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->json(
                [
                    'الرسالة' => 'هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك',
                    'status' => false,
                ],
                403
            );
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
            $subcategory = Category::selection()->whereId($id)->whereNotNull('parent_id')->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$subcategory || !is_numeric($id))
                // رسالة خطأ
                return response()->json(
                    [
                        'الرسالة' => 'هذا العنصر غير موجود',
                        'status' => false,
                    ],
                    403
                );
            // ============= التعديل على التصنيف  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف التصنيف :
            $subcategory->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->json('تم حذف تصنيف الفرعي بنجاح', 201);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->json(
                [
                    'الرسالة' => 'هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك',
                    'status' => false,
                ],
                403
            );
        }
    }
}