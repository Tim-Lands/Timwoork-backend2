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
        // جلب كل الطلبيات
        $orders = Order::selection()->with('cart', function ($q) {
            $q->select('id', 'user_id', 'total_price')
                ->with('user', function ($q) {
                    $q->select('id', 'username')->with('profile', function ($q) {
                        $q->select('id', 'first_name', 'last_name', 'credit', 'user_id', 'withdrawable_amount', 'pending_amount', 'badge_id', 'level_id')
                    ->with([
                        'badge:id,name_ar,name_en',
                        'level:id,name_ar,name_en'
                    ]);
                    });
                });
        })->get();
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
                                   ->with(['cart' => function ($q) {
                                       $q->select('id', 'user_id', 'total_price')->with('user', function ($q) {
                                           $q->select('id', 'username')->with('profile', function ($q) {
                                               $q->select('id', 'first_name', 'last_name', 'user_id', 'badge_id', 'level_id')->with(['badge:id,name_ar,name_en','level:id,name_ar,name_en'])->get();
                                           });
                                       });
                                   },'items' => function ($q) {
                                       $q->select('id', 'order_id', 'uuid', 'title', 'price_product', 'profile_seller_id', 'duration', 'status')
                                       ->with('profileSeller', function ($q) {
                                           $q->select('id', 'profile_id')->with('profile', function ($q) {
                                               $q->select('id', 'user_id', 'first_name', 'last_name', 'level_id', 'badge_id')->with(['user:id,username','badge:id,name_ar,name_en','level:id,name_ar,name_en'])->get();
                                           })->get();
                                       })->get();
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
        $item = Item::select('id', 'order_id', 'uuid', 'title', 'price_product', 'profile_seller_id', 'duration', 'status')
            ->whereId($id)
            ->with(['order' =>function ($q) {
                $q->select('id', 'cart_id')->with(['cart' => function ($q) {
                    $q->select('id', 'user_id', 'total_price')
                      ->with('user', function ($q) {
                          $q->select('id', 'username')->with('profile', function ($q) {
                              $q->select('id', 'first_name', 'last_name', 'user_id', 'badge_id', 'level_id')->with([
                                  'badge:id,name_ar,name_en',
                                  'level:id,name_ar,name_en'])
                              ->get();
                          });
                      });
                }]);
            },
                'profileSeller' => function ($q) use ($product_id) {
                    $q->select('id', 'profile_id', 'seller_level_id', 'seller_badge_id')
                        ->with(['profile' => function ($q) {
                            $q->select('id', 'first_name', 'last_name', 'user_id', 'level_id', 'badge_id')
                            ->with([
                                'user:id,username',
                                'badge:id,name_ar,name_en',
                                'level:id,name_ar,name_en'
                            ]);
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
