<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivedProductController extends Controller
{
    /**
     * activeProduct => دالة تقوم بعملية تنشيط الخدمة
     *
     * @param  mixed $id => id المعرف
     * @return void
     */
    public function activeProduct(mixed $id): JsonResponse
    {
        try {
            $product = Product::find($id);
            if (!$product)
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // ============= تنشيط الخدمة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية تنشيط الخدمة :
            $product->status = Product::PRDUCT_ACTIVE;
            $product->save();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية التنشيط:
            return response()->success('تم تنشيط الخدمة بنجاح', $product);
            // =================================================

        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    /**
     * rejectProduct => دالة تقوم بعملية تنشيط الخدمة
     *
     * @param  mixed $id => id المعرف
     * @return void
     */
    public function rejectProduct(mixed $id): JsonResponse
    {
        try {
            $product = Product::find($id);
            if (!$product)
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // ============= رفض الخدمة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية رفض الخدمة :
            $product->status = Product::PRDUCT_REJECT;
            $product->save();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الرفض:
            return response()->success('تم رفض الخدمة بنجاح', $product);
            // =================================================

        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
