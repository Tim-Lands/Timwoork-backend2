<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * index => عرض جميع الخدمات
     *
     * @return void
     */
    public function index(): JsonResponse
    {
        // جلب جميع الخدمات
        $products = Product::selection()->with(['category', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success('تم العثور على قائمة الخدمات', $products);
    }

    /**
     * show => id  دالة جلب الخدمة معينة بواسطة المعرف
     *
     * @param  mixed $id
     * @return void
     */
    public function show(mixed $id)
    {
        //slug  جلب العنصر بواسطة
        $product = Product::Selection()->whereId($id)->with(['category', 'profileSeller'])->first();
        // شرط اذا كان العنصر موجود
        if (!$product)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);

        // اظهار العنصر
        return response()->success('تم جلب العنصر بنجاح', $product);
    }

    /**
     * productsActived
     *
     * @return JsonResponse
     */
    public function getRroductsActived(): JsonResponse
    {
        // جلب جميع الخدمات التي تم تنشيطها
        $products_actived = Product::selection()->productActive()->with(['category', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success('تم العثور على قائمة الخدمات التي تم تنشيطها', $products_actived);
    }

    /**
     * productsActived
     *
     * @return JsonResponse
     */
    public function getProductsRejected(): JsonResponse
    {
        // جلب جميع الخدمات التي تم رفضها
        $products_rejected = Product::selection()->productReject()->with(['category', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success('تم العثور على قائمة الخدمات التي تم رفضها', $products_rejected);
    }
}
