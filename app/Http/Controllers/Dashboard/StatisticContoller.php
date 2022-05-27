<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticContoller extends Controller
{
    public function __invoke()
    {
        $data = [
            'users'                           => DB::table('users')->count(),
            'admins'                          => DB::table('admins')->count(),
            'categories'                      => DB::table('categories')->where('parent_id', null)->count(),
            'subcategories'                   => DB::table('categories')->where('parent_id', '!=', null)->count(),
            'levels'                          => DB::table('levels')->count(),
            'levels_sellers'                  => DB::table('seller_levels')->count(),
            'badges_sellers'                  => DB::table('seller_badges')->count(),
            'badges'                          => DB::table('badges')->count(),
            'tags'                            => DB::table('tags')->count(),
            'products_wainting_actived'       => DB::table('products')->whereNull('status')->count(),
            'products_actived'                => DB::table('products')->where('status', 1)->count(),
            'products_rejected'               => DB::table('products')->where('status', 0)->count(),
            'five_last_users'                 => DB::table('users')->take(5)->latest()->count(),
            'five_last_orders'                => DB::table('orders')->take(5)->latest()->count(),
            'five_last_products_pendings'     => DB::table('products')->where('status', null)->take(5)->latest()->count(),

        ];

        return response()->success(__("messages.dashboard.statistic_dashboard"), $data);
    }
}
