<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DeleteProductController extends Controller
{
    public function __invoke($id)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // ============================== حذف الصور و المفات ==================================
            // حذف الصورة من مجلد
            if ($product->thumbnail)
                Storage::has("products/thumbnails/{$product->thumbnail}") ? Storage::delete("products/thumbnails/{$product->thumbnail}") : '';
            // جلب الصور مع الخدمة
            $get_galaries_images =  $product->whereId($id)->with(['galaries' => function ($q) {
                $q->select('id', 'path', 'product_id', 'type_file')->where('type_file', 'image')->get();
            }])->first();
            // جلب الملف مع الخدمة
            $get_file_pdf = $product->whereId($id)->with(['galaries' => function ($q) {
                $q->select('id', 'path', 'product_id', 'type_file')->where('type_file', 'file')->get();
            }])->first();
            // وضع الصور في متغير
            $images_galaries = $get_galaries_images['galaries'];
            // وضع الملف في متغير
            $file_galaries   = $get_file_pdf['galaries'][0];

            // حذف الصور اذا وجدت فالمجلد
            if ($images_galaries)
                foreach ($images_galaries as $image) {
                    Storage::has("products/galaries-images/{$image['path']}") ? Storage::delete("products/galaries-images/{$image['path']}") : '';
                }
            // حذف الملف اذا وجدت فالمجلد
            if ($file_galaries)
                Storage::has("products/galaries-file/{$file_galaries['path']}") ? Storage::delete("products/galaries-file/{$file_galaries['path']}") : '';
            // ====================================================================================    
            // ============================== حذف الخدمة ====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الخدمة
            $product->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ==============================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم حذف الخدمة بنجاح', $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}