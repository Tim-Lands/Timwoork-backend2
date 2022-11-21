<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TagRequest;
use App\Models\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * index => دالة عرض كل الوسوم
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // جلب جميع الوسم عن طريق التصفح
        $q = $request->query('name');
        $tags = Tag::Selection()->where('name', 'like', '%' . $q . '%')->paginate(10);
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $tags);
    }

    public function filter(Request $request): JsonResponse
    {
        $q = $request->query('tag');
        // جلب جميع الوسم عن طريق التصفح
        $tags = Tag::Selection()->where('name', 'like', '%' . $q . '%')->paginate(10);
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $tags);
    }


    /**
     * store => دالة اضافة وسم جديد
     *
     * @param  TagRequest $request => انشاء هذا الكائن من اجل عملية التحقيق على المدخلات
     * @return object
     */
    public function store(TagRequest $request): ?object
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name'  => $request->name,
                'label' => $request->name,
                'value' => $request->name
            ];
            // ============= انشاء وسم جديد ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة وسم :
            $tag = Tag::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $tag);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * show => id  دالة جلب وسم معين بواسطة المعرف
     *
     *s @param  string $id => id متغير المعرف
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        //id  جلب العنصر بواسطة
        $tag = Tag::Selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود
        if (!$tag) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $tag);
    }


    /**
     * update => دالة تعديل على الوسم
     *
     * @param  mixed $id
     * @param  TagRequest $request
     * @return object
     */
    public function update(TagRequest $request, mixed $id): ?object
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $tag = Tag::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$tag || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name' => $request->name,
            ];
            // ============= التعديل على الوسم  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على الوسم :
            $tag->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $tag);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => دالة حذف الوسم
     *
     * @param  mixed $id
     * @return object
     */
    public function delete(mixed $id): ?object
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $tag = Tag::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$tag || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= حذف الوسم  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الوسم :
            $tag->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success(__("messages.oprations.delete_success"), $tag);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
