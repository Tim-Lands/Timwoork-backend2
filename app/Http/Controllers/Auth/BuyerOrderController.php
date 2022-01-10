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
        $buyer = Auth::id();
        $items = Item::whereHas('order', function ($q) use ($buyer) {
            $q->whereHas('cart', function ($query) use ($buyer) {
                $query->where('user_id', $buyer);
            })->with('cart');
        })->with(['order', 'profileSeller.profile.user'])->withCount('item_rejected')->paginate($paginate);
        return response()->success(__("messages.oprations.get_all_data"), $items);
    }
}
