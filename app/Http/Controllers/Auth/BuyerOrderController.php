<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;


class BuyerOrderController extends Controller
{

    public function index(Request $request)
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

    public function show($id)
    {
        // جلب الطلبية
        $item = Item::whereId($id)
            ->with(['order.cart.user.profile', 'profileSeller.profile', 'item_rejected', 'item_modified', 'attachments', 'conversation.messages.user.profile', 'conversation.messages.attachments'])
            ->first();

        $logged_user_id = Auth::user()->id;
        $owner_user_id = User::find($item->user_id)->id;
        if ($logged_user_id !== $owner_user_id) {
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
        }
        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }
}
