<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
        $products = Product::selection()->with(['subcategory', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $products);
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
        if (!$product) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
        }

        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $product);
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
        return response()->success(__("messages.dashboard.get_product_actived"), $products_actived);
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
        return response()->success(__("messages.dashboard.get_product_rejected"), $products_rejected);
    }
}
