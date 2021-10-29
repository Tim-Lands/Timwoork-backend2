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
            'badges'                          => DB::table('badges')->count(),
            'tags'                            => DB::table('tags')->count(),
            'products_wainting_actived'       => DB::table('products')->where('status', null)->count(),
            'products_actived'                => DB::table('products')->where('status', 1)->count(),
            'products_rejected'               => DB::table('products')->where('status', 0)->count(),
        ];

        return response()->success('احصائيات لوحة التحكم', $data);
    }
}
