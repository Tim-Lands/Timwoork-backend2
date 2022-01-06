<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Traits\Paypal;
use App\Traits\Stripe;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use Paypal, Stripe;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * createOrderWithItems
     *
     * @return void
     */
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
          
            if (!$cart) {
                return response()->error('لا توجد سلة , الرجاء اعادة عملية الشراء', 422);
            }
            // جلب المعرفات الخدمات المتواجدة في عناصر السلة
            $cart_items = $cart['cart_items']->pluck('product_id');
            if ($cart_items->count() == 0) {
                return response()->error('لا توجد عناصر فالسلة , الرجاء اعادة عملية الشراء', 422);
            }
            // وضع البيانات فالمصفوفة من اجل اضافة طلبيىة
            $data_order = [
                'uuid' => Str::uuid(),
                'cart_id' => $cart->id,
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
            return response()->success('تم انشاء الطلبية', [
                'order' => $order,
                'cart' => $cart
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return $ex;
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 422);
        }
    }

    public function cart_approve()
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments', 'product:title')->get();
            }])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        return $this->approve($cart);
    }

    public function paypal_charge(Request $request)
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments')->get();
            }])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        $pay =  $this->paypal_purchase($request->token, $cart);
        if ($pay) {
            return $this->create_order_with_items();
        }
    }

    public function stripe_charge(Request $request)
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments')->get();
            }])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        $pay = $this->stripe_purchase($request, $cart);
        if ($pay) {
            return $this->create_order_with_items()
        }
    }
}
