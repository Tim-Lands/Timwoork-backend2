<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
}
