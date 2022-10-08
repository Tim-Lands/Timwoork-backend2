<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\Me\Product\ActiveProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    //
    public function updateIsActive($id, ActiveProduct $request)
    {
        try {
            $is_active = $request->is_active;
            // جلب الخدمة
            $product = Product::select('id', 'is_active')
            ->ProductActive()
            ->whereId($id)
            ->where('is_vide', 0)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
            ->where('is_completed', Product::PRODUCT_IS_COMPLETED)
            ->where('is_draft', Product::PRODUCT_IS_NOT_DRAFT)
            ->first();
            // شرط اذا وجد هذه الخدمة
            if (!$product) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            if ($product->is_active == $is_active && $is_active== Product::PRODUCT_ACTIVE) {
                return response()->error(__("messages.seller.actived_product"), Response::HTTP_BAD_REQUEST);
            }
            else if ($product->is_active == $is_active && $is_active== Product::PRODUCT_REJECT) {
                return response()->error(__("messages.seller.disactived_product"), Response::HTTP_BAD_REQUEST);
            }
            /* -------------------- عملية تنشيط الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => $is_active]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            if ($is_active == Product::PRODUCT_ACTIVE)
            return response()->success(__("messages.seller.active_product"), $product);
            else
            return response()->success(__("messages.seller.disactive_product"), $product);
        } catch (Exception $ex) {
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
