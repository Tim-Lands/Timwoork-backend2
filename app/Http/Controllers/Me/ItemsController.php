<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function indexPurchase(Request $request){
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $buyer = Auth::id();
        $items = Item::whereHas('order', function ($q) use ($buyer) {
        $q->whereHas('cart', function ($query) use ($buyer) {
            $query->where('user_id', $buyer);
        })->with(['cart']);
    })->with(['order','profileSeller'=>function ($q){$q->select("id");}])
    ->select('id','uuid','number_product','price_product','order_id','profile_seller_id','status','duration','created_at','updated_at',"title_{$x_localization} AS title")
    ->withCount('item_rejected')->orderBy('created_at', 'DESC')->get();
    return response()->success(__("messages.oprations.get_all_data"), $items);
        }
        catch(Exception $exc){
            echo ($exc);
        }    
}

    public function indexSells(Request $request)
    {
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $seller = Auth::user()->profile->profile_seller->id;
        $items = Item::where('profile_seller_id', $seller)->with('order')->select('id','uuid','number_product','price_product','order_id','status','duration','is_rating','is_item_work','created_at','updated_at',"title_{$x_localization} AS title")
            ->withCount('item_rejected')->orderBy('created_at', 'DESC')->get();
        
        return response()->success(__("messages.oprations.get_all_data"), $items);
        }
        catch(Exception $exc){
            echo($exc);
        }
    }



    public function showSales(Request $request,$id)
    {
        try{
        // جلب الطلبية
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $product_id = Item::whereId($id)->first()->number_product;
        $owner_user_id = Auth::user()->profile->profile_seller->id;
        $item = Item::where('id',$id)->where('profile_seller_id',$owner_user_id)
            ->with([
                'order',
                'order.cart',
                'order.cart.user'=>function($q){
                    $q->select('id','username','email',);
                },
                'order.cart.user.profile:user_id,first_name,last_name,avatar_url,gender'/* =>function($q){
                    $q->select('id','first_name','last_name','avatar_url','gender');
                } */,
                    'item_rejected',
                    'item_modified',
                    'item_date_expired',
                    'attachments:id',
                    'conversation'=>function($q) use($x_localization){
                        $q->select('conversationable_id','id', "title_{$x_localization} AS title");
                    },
                    'conversation.messages',
                    'conversation.messages.attachments'])
            ->select('id','uuid','number_product','price_product','order_id','status','duration','is_rating','is_item_work','created_at','updated_at',"title_{$x_localization} AS title")->first();

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
    catch(Exception $exc){
        echo($exc);
    }
    }

    public function showPurchase($id, Request $request)
    {
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $buyer = Auth::id();

        $product_id = Item::whereId($id)->first()->number_product;
        // جلب الطلبية
        $item = Item::whereHas('order', function ($q) use ($buyer) {
            $q->whereHas('cart', function ($query) use ($buyer) {
                $query->where('user_id', $buyer);
            })->with(['cart']);
        })->where('id',$id)
            ->with([
                'profileSeller'=>function ($q) use($x_localization){
                    $q->select('id',"bio_{$x_localization} AS bio",'profile_id');
                },
                'profileSeller.profile'=>function($q){
                    $q->select('id','first_name','last_name','avatar_url','gender');
                },
                /* 'profileSeller.products' => function ($q) use ($product_id) {
                    $q->select('id', 'profile_seller_id', 'buyer_instruct')->where('id', $product_id);
                }, */
                'item_rejected',
                'item_modified',
                'item_date_expired',
                'attachments',
                'conversation'=>function($q) use($x_localization){
                    $q->select("id","title_{$x_localization} AS title",'conversationable_type','conversationable_id');
                },
                'conversation.messages'=>function($q) use($x_localization){
                    $q->select("id","conversation_id","message_{$x_localization} AS message", 'read_at','created_at','updated_at');
                },
                'conversation.messages.attachments'
            ])->select('id','uuid','number_product','price_product','order_id','profile_seller_id','status','duration','date_expired','created_at','updated_at',"title_{$x_localization} AS title")
            ->first();

        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // تحديد الرسائل كمقروءة

        if ($item->conversation && $item->conversation->messages()) {
            $unreaded_messages = $item->conversation->messages()
                ->whereNull('read_at')
                ->where('user_id', '!=', $buyer)
                ->get();
            foreach ($unreaded_messages as $key => $message) {
                $message->read_at = now();
                $message->save();
            }
        }

        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }
    
    catch(Exception $exc){
        echo($exc);
    }
}
}
