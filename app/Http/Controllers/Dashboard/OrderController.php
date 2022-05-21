<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // التصفح
        $paginate = request()->query('paginate') ? request()->query('paginate') : 10;
        // جلب كل الطلبيات
        $orders = Order::selection()->with('cart', function ($q) {
            $q->select('id', 'user_id')
                ->with('user', function ($q) {
                    $q->select('id', 'username')->with('profile', function ($q) {
                        $q->select('id', 'first_name', 'last_name', 'user_id', 'full_name')
                        ->without(['wise_account','paypal_account','bank_account','bank_transfer_detail','counrty']);
                    });
                });
        })->latest()->paginate($paginate);
        // رسالة نجاح
        return response()->success(__('messages.oprations.get_all_data'), $orders);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب الطلبية مع عناصرها
        $order = Order::selection()->whereId($id)
                                   ->with(['items' => function ($q) {
                                       $q->select('id', 'order_id', 'uuid', 'title', 'duration', 'status', 'profile_seller_id')
                                       ->with('profileSeller', function ($q) {
                                           $q->select('id', 'profile_id')->with('profile', function ($q) {
                                               $q->select('id', 'user_id', 'first_name', 'last_name')->with(['user:id,username'])
                                               ->without(['wise_account','paypal_account','bank_account','bank_transfer_detail','level','badge','counrty'])
                                               ;
                                           })->without('level', 'badge');
                                       });
                                   }])->first();

        if (!$order) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح العملية
        return response()->success(__('messages.oprations.get_data'), $order);
    }

    /**
     * get_order_item
     *
     * @param  mixed $id
     * @return void
     */
    public function get_order_item($id)
    {
        // جلب الطلبية
        $product_id = Item::whereId($id)->first()->number_product;
        //return $product_id;
        $item = Item::select('id', 'order_id', 'uuid', 'title', 'number_product', 'price_product', 'profile_seller_id', 'duration', 'status')
            ->whereId($id)
            ->with(['order' =>function ($q) {
                $q->select('id', 'cart_id')->with(['cart' => function ($q) {
                    $q->select('id', 'user_id')
                      ->with('user', function ($q) {
                          $q->select('id', 'username')->with('profile', function ($q) {
                              $q->select('id', 'first_name', 'last_name', 'user_id', 'badge_id', 'level_id')->with(['badge:id,name_ar,name_en','level:id,name_ar,name_en'])
                                ->without(['wise_account','paypal_account','bank_account','bank_transfer_detail','counrty'])
                              ->get();
                          });
                      });
                }]);
            },
            'profileSeller' => function ($q) use ($product_id) {
                $q->select('id', 'profile_id', 'seller_level_id', 'seller_badge_id')
                    ->with(['profile' => function ($q) {
                        $q->select('id', 'first_name', 'last_name', 'user_id', 'level_id', 'badge_id')
                        ->with(['user:id,username','badge:id,name_ar,name_en','level:id,name_ar,name_en'])
                        ->without(['wise_account','paypal_account','bank_account','bank_transfer_detail','counrty']);
                    },'products' => function ($q) use ($product_id) {
                        $q->select('id', 'profile_seller_id', 'price', 'count_buying')->where('id', $product_id);
                    },'badge:id,name_ar,name_en','level:id,name_ar,name_en']);
            },
                'item_rejected',
                'item_modified',
                'attachments',
            ])
            ->first();
        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }
}
