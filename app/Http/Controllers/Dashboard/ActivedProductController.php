<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
            if (!$product) {
                // رسالة خطأ
                return response()->error(__('messages.errors.element_not_found'), Response::HTTP_NOT_FOUND);
            }

            // ============= تنشيط الخدمة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية تنشيط الخدمة :
            $product->status = Product::PRODUCT_ACTIVE;
            $product->save();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية التنشيط:
            return response()->success(__('messages.dashboard.active_status_product'), $product);
            // =================================================
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
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
            if (!$product) {
                // رسالة خطأ
                return response()->error(__('messages.errors.element_not_found'), Response::HTTP_NOT_FOUND);
            }

            // ============= رفض الخدمة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية رفض الخدمة :
            $product->status = Product::PRODUCT_REJECT;
            $product->save();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الرفض:
            return response()->success(__('messages.dashboard.reject_status_product'), $product);
            // =================================================
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ :
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        }
    }
}
