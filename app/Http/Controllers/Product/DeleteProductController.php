<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteProductController extends Controller
{
    public function __invoke($id)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            // ============================== حذف الخدمة ====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الخدمة
            $product->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ==============================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.delete_success"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
