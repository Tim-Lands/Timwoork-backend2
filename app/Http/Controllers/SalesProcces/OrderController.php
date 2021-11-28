<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function createOrderWithItems()
    {
        try {
            // متغير به سلة المشتري
            $cart = Cart::selection()->where('user_id', 2)->with(['product', 'cart_developments'])->get()[0];
            $product_cart = $cart;
            // الخدمات المتواجدة في سلة المشتري
            $count_products = $cart->pluck('product_id')->count();
            return  $product_cart;


            // وضع البيانات فالمصفوفة من اجل اضافة طلبيىة
            // return $cart->pluck('product_id')[1];
            $data_order = [
                'uuid' => Str::uuid(),
                'cart_id' => request('cart_id'),
                'payment_id' => request('payment_id'),
            ];

            $data_items = [];
            // ============= انشاء طلبية جديدة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة الطلبية :
            $order_id = Order::insertGetId($data_order);

            if ($count_products == 1)
                $data_items = [
                    "order_id" => $order_id,
                    "uuid" => Str::uuid(),
                    "statu" => Item::STATUS_NEW_REQUEST,
                    "number_product" => $cart->pluck('product_id')[0],

                ];
            else
                foreach ($cart->pluck('product_id') as $item => $value) {
                    $data_items[] = [
                        'number_product' => $value,
                        'status' => Item::STATUS_NEW_REQUEST
                    ];
                }

            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء الطلبية');
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    public function createItemByOrder(mixed $id)
    {
        try {
            // متغير به سلة المشتري
            $cart = Cart::selection()->where('id', request('cart_id'))->with(['product', 'cart_developments'])->first();

            $duration_product = $cart['product']['duration'];

            $duration_development = $cart['cart_developments']->sum('duration');

            $duration_total = $duration_product + $duration_development;
            $data_item = [
                'status'   => Item::STATUS_NEW_REQUEST,
                'duration' => $duration_total,
                ''
            ];
            return $cart['cart_developments']->sum('duration');

            // مدة الزمنية للخدمة


            // ============= انشاء عنصر جديد فالسلة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة تصنيف :
            //$cart = Order::create($data_order);
            // شرط في حالة ما كانت هناك تطويرات مضافة
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $cart);
        } catch (Exception $ex) {
        }
    }

    public function delete(mixed $id)
    {
    }
}
