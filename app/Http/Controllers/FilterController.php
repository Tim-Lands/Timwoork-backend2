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
        $res = Product::filter()->productActive()
            ->with([
                'profileSeller' => function ($q) {
                    $q->with('profile')
                        ->without('languages', 'skills', 'professions');
                },
                'ratings',
                'subcategory' => function ($q) {
                    $q->select('id', 'parent_id', 'name_ar')
                        ->with('category', function ($q) {
                            $q->select('id', 'name_ar')
                                ->without('subcategories');
                        })->withCount('products');
                },
            ])->withAvg('ratings', 'rating')
            ->withCount('ratings as rats_count')
            ->paginate($paginate);
        if (!$res->isEmpty()) {
            return response()->success('تمت عملية الفلترة بنجاح', $res);
        } else {
            return response()->success('لم يتم العثور على نتائج', [], 204);
        }
    }
}
