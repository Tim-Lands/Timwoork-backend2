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

    public function show($id)
    {
        $product_id = Item::whereId($id)->first()->number_product;
        // جلب الطلبية
        $item = Item::whereId($id)
            ->with([
                'order.cart.user.profile',
                'profileSeller.profile',
                'profileSeller.products' => function ($q) use ($product_id) {
                    $q->select('id', 'profile_seller_id', 'buyer_instruct')->where('id', $product_id);
                },
                'item_rejected',
                'item_modified',
                'item_date_expired',
                'attachments',
                'conversation.messages.user.profile',
                'conversation.messages.attachments'
            ])
            ->first();

        $logged_user_id = Auth::user()->id;
        $owner_user_id =  $item->order->cart->user->id;
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
