<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StatisticContoller extends Controller
{
    public function __invoke()
    {
        $products_accepted = Product::where('status', 1)->count();
        $products_rejected = Product::where('status', 0)->count();
        $products_total = Product::where('is_completed', 1)->count();
        $products_pending = Product::whereNull('status')
                            ->where('is_completed', 1)
                            ->count();
        $data = [
            'users'                           => DB::table('users')->count(),
            'admins'                          => DB::table('admins')->count(),
            'categories'                      => DB::table('categories')->where('parent_id', null)->count(),
            'subcategories'                   => DB::table('categories')->where('parent_id', '!=', null)->count(),
            'levels'                          => DB::table('levels')->count(),
            'levels_sellers'                  => DB::table('seller_levels')->count(),
            'badges_sellers'                  => DB::table('seller_badges')->count(),
            'badges'                          => DB::table('badges')->count(),
            'products'                        => $products_total,
            'tags'                            => DB::table('tags')->count(),
            'products_wainting_actived'       => $products_pending,
            'products_actived'                => $products_accepted,
            'products_rejected'               => $products_rejected,
            'five_last_users'                 => DB::table('users')->take(5)->latest()->count(),
            'five_last_orders'                => DB::table('orders')->take(5)->latest()->count(),
            'five_last_products_pendings'     => DB::table('products')->where('status', null)->take(5)->latest()->count(),
            "profile_sellers"                 => DB::table('profile_sellers')->count(),
            "buyers"                          => DB::table('profiles')->where('is_seller',0)->count(),
            "products_disactived"             => DB::table("products")->whereNull('status')->count()
        ];

        return response()->success(__("messages.dashboard.statistic_dashboard"), $data);
    }
}
