<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
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
        $seller = Auth::user()->profile->profile_seller->id;
        $items = Item::where('profile_seller_id', $seller)->with(['order.cart.user.profile'])
            ->withCount('item_rejected')->paginate($paginate);
        return response()->success(__("messages.oprations.get_all_data"), $items);
    }
}
