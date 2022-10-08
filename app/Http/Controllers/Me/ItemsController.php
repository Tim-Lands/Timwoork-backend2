<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        echo ('hi');
        //$paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $item_type=0;
        if ($request->has('type') && ($request->input('type')==Item::ITEM_TYPE_PURCHASE or $request->input('type') == Item::ITEM_TYPE_SELL ) ) {
            
            $item_type = $request->input('type');
         }
         else{
            return response()->error(__("messages.item.item_type_not_found"));
         }
         
        if($item_type==Item::ITEM_TYPE_SELL){

        $seller = Auth::user()->profile->profile_seller->id;
        $items = Item::where('profile_seller_id', $seller)->with(['order.cart.user.profile'])
            ->withCount('item_rejected')->orderBy('created_at', 'DESC')->get();
        
        return response()->success(__("messages.oprations.get_all_data"), $items);
        }

        else if($item_type == Item::ITEM_TYPE_PURCHASE){

            $buyer = Auth::id();
            $items = Item::whereHas('order', function ($q) use ($buyer) {
            $q->whereHas('cart', function ($query) use ($buyer) {
                $query->where('user_id', $buyer);
            })->with('cart');
        })->with(['order', 'profileSeller.profile.user'])->withCount('item_rejected')->orderBy('created_at', 'DESC')->get();
        return response()->success(__("messages.oprations.get_all_data"), $items);
        }
        else{
            return "sharaf else";
            
        }

    }
}
