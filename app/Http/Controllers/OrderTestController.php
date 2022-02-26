<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\ItemDateExpired;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderTestController extends Controller
{
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
                ->isnotbuying()
                ->first();

            if (!$cart) {
                return response()->error(__("messages.cart.cart_not_found"), Response::HTTP_NOT_FOUND);
            }
            // جلب المعرفات الخدمات المتواجدة في عناصر السلة
            $cart_items = $cart['cart_items']->pluck('product_id');
            if ($cart_items->count() == 0) {
                return response()->error(__('messages.cart.cartitem_found'), Response::HTTP_NOT_FOUND);
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
                $title_product = $cart['cart_items'][$key]->product_title;
                // جلب تطويرات المضافة في العنصر السلة
                // جلب كمية الخدمة
                $quantity = $cart['cart_items'][$key]->quantity;
                // المدة الزمنية للتطويرات
                $duration_developments = $cart['cart_items'][$key]['cartItem_developments']->sum('duration');
                // حساب مدة الخدمة مع تطويراتها
                $duration_total = ($duration_product + $duration_developments) * $quantity;
                // انشاء الطلبية
                $item = Item::create([
                    'uuid' => Str::uuid(),
                    'title' => $title_product,
                    'profile_seller_id' => $user_seller,
                    'order_id' => $order->id,
                    'number_product' => $value,
                    'price_product' => $cart['cart_items'][$key]->price_product,
                    'duration' => $duration_total,
                    'status' => Item::STATUS_PENDING,
                ]);
                // انشاء توقيت انهاء الطلبية
                ItemDateExpired::create([
                    'date_expired' => Carbon::now()
                                        ->addDays(Item::EXPIRED_TIME_NNTIL_SOME_DAYS)
                                        ->toDateTimeString(),
                    'item_id'      => $item->id,
                ]);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            /* -------------------------------------------------------------------------- */
            return response()->success('تم انشاء الطلبية', [
                'order' => $order->with('items')->first(),
                'cart' => $cart
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
