<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerOrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $buyer = Auth::user()->profile->profile_seller()->id;
        $items = Item::with(['profileSeller.profile.user'])->where('profile_seller_id', $buyer)->paginate($paginate);
        return response()->success('لقد تم جلب مشترياتك بنجاح', $items);
    }
}
