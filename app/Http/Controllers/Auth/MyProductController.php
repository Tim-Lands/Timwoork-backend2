<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
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
        return response()->success('لقد تم العثور على خدماتك', $products);
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
        return response()->success('لقد تم العثور على خدماتك', $products);
    }

    /**
     * عرض الخدمات الموقفة مؤقتا من طرف المستخدم
     */
    public function paused(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->productActive()->where('is_active', false)->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success('لقد تم العثور على خدماتك', $products);
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
        return response()->success('لقد تم العثور على خدماتك', $products);
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
        return response()->success('لقد تم العثور على خدماتك', $products);
    }

    /**
     * عرض الخدمات المحفوظة وغير مكتملة
     */
    public function drafts(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $user = Auth::user();
        $products = $user->profile->profile_seller->products()->where('is_draft', true)->paginate($paginate)
            ->makeHidden([
                'buyer_instruct', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
        return response()->success('لقد تم العثور على خدماتك', $products);
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
            ->where('is_completed', 1)
            ->first()
            ->makeHidden([
                'buyer_instruct','status', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
            ;
            // شرط اذا وجد هذه الخدمة
            if (!$product) {
                return response()->error('لا يوجد هذا العنصر', 422);
            }
            if ($product->is_active == Product::PRODUCT_ACTIVE) {
                return response()->error('تم تنشيط الخدمة من قبل', 422);
            }
            /* -------------------- عملية تنشيط الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => Product::PRODUCT_ACTIVE]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            return response()->success('تم تنشيط الخدمة', $product);
        } catch (Exception $ex) {
            return $ex;
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
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
            ->where('is_completed', 1)
            ->first()
            ->makeHidden([
                'buyer_instruct','status', 'content', 'profile_seller_id', 'category_id', 'duration'
            ]);
            ;
            // شرط اذا وجد هذه الخدمة
            if (!$product) {
                return response()->error('لا يوجد هذا العنصر', 422);
            }
            if ($product->is_active == 0) {
                return response()->error('الخدمة معطلة', 422);
            }
            /* -------------------- عملية تنشيط الخدمة من طرف البائع -------------------- */
            $product->update(['is_active' => Product::PRODUCT_REJECT]);
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح
            return response()->success('تم تعطيل الخدمة', $product);
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
