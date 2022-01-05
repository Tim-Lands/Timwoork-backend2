<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use Illuminate\Support\Str;

trait CreateOrder
{
    public function create_order_with_items()
    {
        try {
            //سلة المشتري
            $cart = Cart::selection()
                ->with(['cart_items' => function ($q) {
                    $q->with('cartItem_developments')->get();
                }])
                ->where('user_id', Auth::user()->id)
                ->where('is_buying', 0)
                ->first();

            // جلب المعرفات الخدمات المتواجدة في عناصر السلة
            $cart_items = $cart['cart_items']->pluck('product_id');
            if ($cart_items->count() == 0 || !$cart) {
                return response()->error('لا توجد عناصر فالسلة , الرجاء اعادة عملية الشراء');
            }
            // وضع البيانات فالمصفوفة من اجل اضافة طلبيىة
            $data_order = [
                'uuid' => Str::uuid(),
                'cart_id' => $cart->id,
                'payment_id' => 1,
            ];
            // مصفوفة من اجل وضع فيها عناصر الطلبية
            $data_items = [];
            /* ---------------------------- انشاء طلبية جديدة --------------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة الطلبية :
            $order = Order::create($data_order);
            // وضع السلة مباعة
            $cart->is_buying = 1;
            $cart->save();
            // وضع عناصر السلة في عناصر الطلبية من اجل اكمال عملية البيع
            foreach ($cart_items as $key => $value) {
                // جلب معلومات البائع
                $user_seller = Product::select('id', 'profile_seller_id')
                    ->where('id', $value)
                    ->with('profileSeller', fn ($q) => $q->select('id')->without(['skills']))
                    ->first()['profileSeller']->id;
                // المدة الزمنية للخدمة
                $duration_product = $cart['cart_items'][$key]->duration_product;
                // جلب تطويرات المضافة في العنصر السلة
                // جلب كمية الخدمة
                $quantity = $cart['cart_items'][$key]->quantity;
                // المدة الزمنية للتطويرات
                $duration_developments = $cart['cart_items'][$key]['cartItem_developments']->sum('duration');
                // حساب مدة الخدمة مع تطويراتها
                $duration_total = ($duration_product + $duration_developments) * $quantity;
                // وضع البيانات العناصر السلة في مصفوفة العناصر الطلبية
                $data_items[] = [
                    'uuid' => Str::uuid(),
                    'profile_seller_id' => $user_seller,
                    'order_id' => $order->id,
                    'number_product' => $value,
                    'price_product' => $cart['cart_items'][$key]->price_product,
                    'duration' => $duration_total,
                    'status' => Item::STATUS_PENDING_REQUEST,
                ];
            }
            // اضافة عناصر الطلبية
            $order->items()->createMany($data_items);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            /* -------------------------------------------------------------------------- */
            return response()->success('تم انشاء الطلبية', $order);
        } catch (Exception $ex) {
            DB::rollBack();
            return $ex;
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
