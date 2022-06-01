<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FilterController extends Controller
{
    public function __invoke(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
        $res = Product::select('id', 'title', 'slug', 'price', 'ratings_avg', 'count_buying', 'thumbnail', 'ratings_count', 'category_id', 'profile_seller_id', 'duration', 'content', 'created_at')
            ->filter()
            ->productActive()
            ->where('is_active', 1)
            ->with([
                'profileSeller' => function ($q) {
                    $q->with(['profile'=> function ($q) {
                        $q->select('id', 'user_id', 'first_name', 'last_name')
                            ->with('user:id,username');
                    }])
                    ->without('languages', 'skills', 'professions');
                },
                'ratings',
                'subcategory' => function ($q) {
                    $q->select('id', 'parent_id', 'name_ar', 'name_en', 'name_fr')
                        ->with('category', function ($q) {
                            $q->select('id', 'name_ar', 'name_en', 'name_fr')
                                ->without('subcategories');
                        })->withCount('products');
                },
            ])/*->withAvg('ratings', 'rating')*/
            ->withCount('ratings as rats_count')
            ->where('is_completed', 1)
            ->paginate($paginate);



        if (!$res->isEmpty()) {
            return response()->success(__("messages.filter.filter_success"), $res);
        } else {
            return response()->success(__("messages.filter.filter_field"), [], 204);
        }
    }
}
