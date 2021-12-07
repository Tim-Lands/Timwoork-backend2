<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function createOrderWithItems()
    {
        try {
            //سلة المشتري
            $cart = Cart::selection()->with(['subcarts' => function ($q) {
                $q->with('subcart_developments')->get();
            }])->where('user_id', 3)->where('is_buying', 0)->first();

            // جلب المعرفات الخدمات المتواجدة في عناصر السلة
            $subcats = $cart['subcarts']->pluck('product_id');
            //return $cart['subcarts'][1]['subcart_developments']->sum('duration');

            // وضع البيانات فالمصفوفة من اجل اضافة طلبيىة
            $data_order = [
                'uuid' => Str::uuid(),
                'cart_id' => $cart->id,
                'payment_id' => request('payment_id'),
            ];

            $data_items = [];
            foreach ($subcats as $key => $value) {
                // جلب الخدمة
                $product = Product::whereId($value);
                // المدة الزمنية للخدمة
                $duration_product = $product->first()->duration;
                // جلب تطويرات المضافة في العنصر السلة
                $developments = $cart['subcarts'][$key]['subcart_developments'];
                // جلب كمية الخدمة
                $quantity = $cart['subcarts'][$key]->quantity;
                // المدة الزمنية للتطويرات
                $duration_developments =  $developments->sum('duration');

                $duration_total = ($duration_product + $duration_developments) * $quantity;

                $price_product = ($cart['subcarts'][$key]->price_product + $developments->sum('price')) * $quantity;


                $data_items[] = [
                    'uuid' => Str::uuid(),
                    'order_id' => 1,
                    'number_product' => $value,
                    'price_prduct' => $price_product,
                    'duration' => $duration_total,
                    'status' => Item::STATUS_NEW_REQUEST,
                ];
            }
            return $data_items;
            // ============= انشاء طلبية جديدة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة الطلبية :
            $order = Order::create($data_order);



            //$order_id = Order::insertGetId($data_order);

            // if ($count_products == 1)
            //     $data_items = [
            //         "order_id" => $order_id,
            //         "uuid" => Str::uuid(),
            //         "statu" => Item::STATUS_NEW_REQUEST,
            //         "number_product" => $cart->pluck('product_id')[0],

            //     ];
            // else
            //     foreach ($cart->pluck('product_id') as $item => $value) {
            //         $data_items[] = [
            //             'number_product' => $value,
            //             'status' => Item::STATUS_NEW_REQUEST
            //         ];
            //     }

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
