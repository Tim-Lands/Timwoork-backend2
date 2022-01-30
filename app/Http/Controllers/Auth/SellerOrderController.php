<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class SellerOrderController extends Controller
{

    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ? $request->query('paginate') : 10;
        $seller = Auth::user()->profile->profile_seller->id;
        $items = Item::where('profile_seller_id', $seller)->with(['order.cart.user.profile'])
            ->withCount('item_rejected')->paginate($paginate);
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

        // تحديد الرسائل كمقروءة

        if ($item->conversation && $item->conversation->messages()) {
            $unreaded_messages = $item->conversation->messages()
                ->whereNull('read_at')
                ->where('user_id', '!=', $owner_user_id)
                ->get();
            foreach ($unreaded_messages as $key => $message) {
                $message->read_at = now();
                $message->save();
            }
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }
}
