<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MyProductController extends Controller
{
    /**
     * عرض جميع الخدمات الخاصة بالمستخدم
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * عرض الخدمات المنشورة فقط الخاصة بالمستخدم
     */

    public function published(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->productActive()->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * عرض الخدمات الموقفة مؤقتا من طرف المستخدم
     */
    public function paused(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->productActive()
                         ->where('is_active', Product::PRODUCT_REJECT)
                         ->paginate($paginate)
                         ->makeHidden([
                            'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
                        ]);

        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * عرض الخدمات المرفوضة للمستخدم
     */
    public function rejected(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->productReject()->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * عرض الخدمات التي تنتظر التفعيل من الادارة
     */
    public function pending(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->whereNull('status')->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * عرض الخدمات المحفوظة وغير مكتملة
     */
    public function drafts(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->where('is_draft', Product::PRODUCT_IS_DRAFT)->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * active_product_by_user => تنشيط الخدمة من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function active_product_by_seller($id)
    {
        try {
            // جلب الخدمة
            $product = Product::ProductActive()
            ->whereId($id)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
            ->where('is_completed', Product::PRODUCT_IS_COMPLETED)
            ->where('is_draft', Product::PRODUCT_IS_NOT_DRAFT)
            ->first();
            // شرط اذا وجد هذه الخدمة
            if (!$product) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            if ($product->is_active == Product::PRODUCT_ACTIVE) {
                return response()->error(__("messages.seller.actived_product"), Response::HTTP_NOT_FOUND);
            }
            /* -------------------- عملية تنشيط الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => Product::PRODUCT_ACTIVE]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            return response()->success(__("messages.seller.active_product"), $product);
        } catch (Exception $ex) {
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
    * active_product_by_user => تعطيل الخدمة من قبل البائع
    *
    * @param  mixed $id
    * @return void
    */
    public function disactive_product_by_seller($id)
    {
        try {
            // جلب الخدمة
            $product = Product::ProductActive()
            ->whereId($id)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
            ->where('is_completed', Product::PRODUCT_IS_COMPLETED)
            ->where('is_draft', Product::PRODUCT_IS_NOT_DRAFT)
            ->first();
            // شرط اذا لم يجد هذه الخدمة
            if (!$product) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            if ($product->is_active == 0) {
                return response()->error(__("messages.seller.disactived_product"), Response::HTTP_NOT_FOUND);
            }
            /* -------------------- عملية تعطيل الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => Product::PRODUCT_REJECT]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            return response()->success(__("messages.seller.disactive_product"), $product);
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * product => عرض الخدمة الواحدة للبائع
     *
     * @param  mixed $id
     * @return void
     */
    public function product($id)
    {
        // جلب الخدمة
        $product = Product::whereId($id)
                            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                            ->with(['subcategory.category','ratings','developments','product_tag','galaries','file','video','shortener','profileSeller.profile' => function ($q) {
                                $q->with(['badge','level','wallet']);
                            }])
                            ->first();
        // شرط اذا لم يتم ايجاد الخدمة
        if (!$product) {
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $product);
    }
}
