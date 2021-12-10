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
        $seller = $request->query('seller_name') ? $request->query('seller_name') : '';
        $rating = $request->query('rating') ? $request->query('rating') : '';
        $res = Product::filter()->productActive()
            ->with([
                'profileSeller' => function ($q) {
                    $q->with('profile')
                        ->without('languages', 'skills', 'professions');
                },
                'ratings'
            ])->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($paginate);
        return response()->success('found', $res);
    }
}
