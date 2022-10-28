<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FilterController extends Controller
{
    public function __invoke1(Request $request)
    {
        $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
        $res = Product::select('id', "title_{$xlocalization} AS title" ,'slug', 'price', 'ratings_avg', 'count_buying', 'thumbnail', 'ratings_count', 'category_id', 'profile_seller_id', 'duration', "content_{$xlocalization} AS content",'created_at')
            ->filter()
            ->productActive()
            ->where('is_active', 1)
            ->with([
                'profileSeller' => function ($q) {
                        $q->select('profile_id','id','seller_badge_id','seller_level_id');
                
                },
                'profileSeller.level'=>function($q) use($xlocalization){
                    $q->select('id', "name_{$xlocalization} AS name");
                },
                'profileSeller.badge'=>function($q) use($xlocalization){
                    $q->select('id', "name_{$xlocalization} AS name");
                },
                'profileSeller.profile'=>function($q) use($xlocalization){
                        $q->select('id', 'first_name','last_name', 'avatar_url','gender','user_id','avatar','full_name')->without(['level','badge','wise_account','paypal_account']);
                },
                'profileSeller.profile.user',

                'subcategory' => function ($q) use($xlocalization) {
                    $q->select('id', 'parent_id', "name_{$xlocalization} AS name")
                        ->with('category', function ($q) use($xlocalization) {
                            $q->select('id', "name_{$xlocalization} AS name")
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
    public function __invoke(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
        $res = Product::select('id', 'title', 'title_ar', "title_fr", "title_en" ,'slug', 'price', 'ratings_avg', 'count_buying', 'thumbnail', 'ratings_count', 'category_id', 'profile_seller_id', 'duration', 'content', 'content_ar', 'content_fr', 'content_en' ,'created_at')
            ->filter()
            ->productActive()
            ->where('is_active', 1)
            ->with([
                'profileSeller' => function ($q) {
                    $q->with(['profile'=> function ($q) {
                        $q->select('*')
                            ->with('user:id,username')
                            ->without('bank_account', 'bank_transfer_detail', 'paypal_account', 'wise_account', 'badge', 'level');
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
