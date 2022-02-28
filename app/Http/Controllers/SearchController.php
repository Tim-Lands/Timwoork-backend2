<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 5;
        $res = Product::select('id', 'title', 'slug', 'price', 'count_buying', 'thumbnail', 'ratings_count', 'category_id', 'profile_seller_id')
            ->filter()
            ->productActive()
            ->with([
                'profileSeller' => function ($q) {
                    $q->select('id', 'profile_id')
                    ->with('profile', function ($q) {
                        $q->select('id', 'first_name', 'last_name', 'user_id')
                            ->with('user:id,username');
                    })
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
            ])->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->take($limit);

        if (!$res->isEmpty()) {
            return response()->success(__("messages.filter.filter_success"), $res);
        } else {
            return response()->success(__("messages.filter.filter_field"), [], Response::HTTP_NO_CONTENT);
        }
    }
}
