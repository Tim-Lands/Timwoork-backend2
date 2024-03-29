<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\TypePayment;
use App\Models\User;
use App\Traits\Paypal;
use App\Traits\Stripe;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    use Paypal, Stripe;
    public function __construct()
    {
        $this->middleware(['auth:sanctum','abilities:user']);
    }

    /**
     * index => دالة عرض السلة
     *
     * @return void
     */
    public function index()
    {
        // عرض السلة المستخدم
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->select('*')
                    ->with(['cartItem_developments' => function ($q) {
                        $q->select('development_id', 'title', 'duration', 'price')->get();
                    }, 'product' => fn ($q) => $q->select('id', 'title', 'price', 'duration')]);
            },'cart_payments' =>function ($q) {
                $q->select('name_ar', 'name_en', 'status')
                ->where('status', 1)
                ->get();
            }])
            ->withCount('cart_items')
            ->where('user_id', Auth::user()->id)
            ->isnotbuying()
            ->first();
        // في حالة ما اذا كانت السلة فارغة
        if (!$cart) {
            $cart = [
                'cart_items_count' => 0,
                'cart_items' => [],
                'cart_payment' => [],
                'is_buying' => 0,
                //'price_with_tax' => 0.00,
                //'tax' => 0.00,
                'total_price' => 0.00,
                'user_id' => auth()->user()->id,
            ];
        }
        // اظهار السلة مع عناصرها
        return response()->success(__('messages.oprations.get_data'), $cart);
    }

    /**
     * store => دالة انشاء عنصر جديد في السلة
     *
     * @param  CartRequest $request
     * @return Response
     */
    public function store(CartRequest $request)
    {
        try {
            // جلب عنصر الطلبية
            $item = Item::select('id', 'number_product', 'profile_seller_id', 'status', 'order_id')
                        ->with(['order' =>function ($q) {
                            $q->select('id', 'cart_id')->with('cart', function ($q) {
                                $q->where('user_id', Auth::id())->isbuying();
                            });
                        },'profileSeller' => function ($q) {
                            $q->select('id', 'profile_id')->with('profile', function ($q) {
                                $q->select('id', 'user_id')->with('user:id');
                            });
                        }])
                        ->where('is_item_work', 1)
                        ->where('number_product', $request->product_id)
                        ->first();
            // جلب المشتري
            $user_buyer = $item->order->cart->user_id ?? null;
            // جلب الخدمة
            $product_in_item = $item->number_product ?? null;

            if ($user_buyer == Auth::id() && $product_in_item == $request->product_id) {
                if ($item) {
                    return response()->error(__("messages.cart.product_work"), Response::HTTP_FORBIDDEN);
                }
            }
            // فحص اذا كان الحساب مفعل ام لا
            if (!Auth::user()->profile->is_completed) {
                return response()->error(__("messages.product.profile_not_complete"), Response::HTTP_BAD_REQUEST);
            }
            // جلب سلة المستخدم
            $cart = Cart::where('user_id', Auth::user()->id);
            // اذا كانت هناك سلة مباعة
            $cart_found_buying =  Cart::where('user_id', Auth::user()->id)->isbuying()->exists();
            // اذا كانت هناك سلة غير مباعة
            $cart_found_not_buying =  Cart::where('user_id', Auth::user()->id)->isnotbuying()->exists();
            // وضع البيانات فالمصفوفة من اجل اضافة السلة
            $data_cart = [
                'user_id' => Auth::user()->id,
            ];
            // الخدمة المضافة في السلة
            $product = Product::whereId($request->product_id)->first();

            // شرط اذا كانت الخدمة للمستخدم المشتري
            if (Auth::user()->profile->profile_seller) {
                if ($product->profile_seller_id == Auth::user()->profile->profile_seller->id) {
                    return response()->error(__('messages.cart.product_not_buying'), Response::HTTP_BAD_REQUEST);
                }
            }
            // وضع البيانات فالمصفوفة من اجل اضافة عناصر فالسلة السلة
            $data_cart_items = [
                'product_id'    => $request->product_id,
                'product_title' => $product->title,
                'product_title_ar' => $product->title_ar,
                'product_title_en' => $product->title_en,
                'product_title_fr' => $product->title_fr,
            ];
            // شرط في حالة وجود الكمية
            if ($request->has('quantity')) {
                $data_cart_items['quantity'] = (int)$request->quantity <= 0  ? 1 : (int)$request->quantity;
            } else {
                $data_cart_items['quantity'] = 1;
            }

            // شرط في حالة ما اذا قام المستخدم بارسال تطويرات
            if ($request->has('developments')) {
                if ($this->check_found_developments($request->developments, $request->product_id) == 0) {
                    return response()->error(__('messages.cart.same_developments'), Response::HTTP_NOT_FOUND);
                }
                // سعر التطويرات المدخلة
                $price_developments = Product::whereId($request->product_id)
                                ->with('developments', function ($q) use ($request) {
                                    $q->whereIn('id', $request->developments);
                                })->first()['developments']->sum('price');
                $product_unit = $product->price + $price_developments;
                // وضع السعر
                $data_cart_items['price_unit'] = $product_unit;
            } else {
                // وضع السعر
                $data_cart_items['price_unit'] = $product->price;
            }
            // فحص عدد الخدمات المسموح الشراء به
            if (!quantity_cheked($request->quantity, $data_cart_items['price_unit'])) {
                return response()->error(__('messages.cart.can_not_buying'), Response::HTTP_BAD_REQUEST);
            }
            /* ---------------------------- انشاء عنصر فالسلة --------------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // شرط اذا لم توجد اي سلة او توجد سلل مباعة و لا توجد سلة غير مباعة :
            if ($cart->count() == 0 || ($cart_found_buying && !$cart_found_not_buying)) {
                $new_cart = Cart::create($data_cart);
                // وضع معرف السلة في مصفوفة العنصر
                $data_cart_items['cart_id'] = $new_cart->id;
                // انشاء عنصر جديد
                $cart_item = CartItem::create($data_cart_items);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $this->add_developments($cart_item, collect($request->developments));
                }
                // جلب العنصر المضاف حديثا
                $new_cart = Cart::where('user_id', Auth::user()->id)->isnotbuying();
                // عمليات حساب السعر المتواجد في السلة
                $this->calculate_price($new_cart, $cart_item, $data_cart_items['quantity']);
                // جلب الانواع البوابات المتوفرة
                $type_payments = TypePayment::selection()->get();
                /* ---------------------- // عمليات حسابية على كل بوابة --------------------- */
                $total = $new_cart->first()['total_price'];
                // اضافة مصفوفة من اجل عمليات الحسابية
                $calculate_with_payments = array_map(function ($key) use ($total) {
                    // متغير الرسوم
                    $tax = 0;
                    // اذا كانت النسبة فوق 1
                    if ($key['precent_of_payment'] >= 1) {
                        // اذا كانت الرسوم فوق 1
                        if (($total * $key['precent_of_payment'] / 100) >= 1) {
                            $tax = $total * $key['precent_of_payment'] / 100;
                        } else {
                            $tax = 1;
                        }
                    }
                    return[
                            'type_payment_id' => $key['id'],
                            'total' => $total,
                            'tax' => $tax + $key['value_of_cent'],
                            'total_with_tax' => $total + $tax + $key['value_of_cent'],
                    ];
                }, $type_payments->toArray());
                //  عملية الاضافة
                $new_cart->first()->cart_payments()->sync($calculate_with_payments);
            }
            // شرط اذا توجد سلة مباعة و سلة غير مباعة او توجد سلة غير مباعة و لا توجد سلة مباعة :
            elseif (($cart_found_buying && $cart_found_not_buying) || (!$cart_found_buying && $cart_found_not_buying)) {
                // جلب العنصر
                $cart_item_found = CartItem::whereCartId($cart->isnotbuying()->first()->id)
                    ->where('product_id', $request->product_id)
                    ->wherehas('cart', function ($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->first();
                // شرط اذا كان العنصر موجود
                if ($cart_item_found) {
                    // رسالة خطأ
                    return response()->error(__('messages.cart.cartitem_found'), Response::HTTP_NOT_FOUND);
                }
                // جلب السلة المستخدم الغير مباعة
                $new_cart =  Cart::where('user_id', Auth::id())->with('cart_payments')->isnotbuying();
                // وضع معرف السلة في مصفوفة العنصر
                $data_cart_items['cart_id'] = $new_cart->first()->id;
                // انشاء عنصر جديد
                $cart_item = CartItem::create($data_cart_items);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $this->add_developments($cart_item, collect($request->developments));
                }
                // عمليات حساب السعر المتواجد في السلة
                $this->calculate_price($new_cart, $cart_item, $data_cart_items['quantity']);

                // جلب الانواع البوابات المتوفرة
                $type_payments = TypePayment::selection()->get();
                /* ---------------------- // عمليات حسابية على كل بوابة --------------------- */
                $total = $new_cart->first()['total_price'];
                // اضافة مصفوفة من اجل عمليات الحسابية
                $cart_payments = array_map(function ($key) use ($total) {
                    // متغير الرسوم
                    $tax = 0;
                    // اذا كانت النسبة فوق 1
                    if ($key['precent_of_payment'] >= 1) {
                        // اذا كانت الرسوم فوق 1
                        if (($total * $key['precent_of_payment'] / 100) >= 1) {
                            $tax = $total * $key['precent_of_payment'] / 100;
                        } else {
                            $tax = 1;
                        }
                    }
                    return[
                            'type_payment_id' => $key['id'],
                            'total' => $total,
                            'tax' => $tax + $key['value_of_cent'],
                            'total_with_tax' => $total + $tax + $key['value_of_cent'],
                    ];
                }, $type_payments->toArray());
                //  عملية الاضافة
                if ($new_cart->first()->cart_payments->count() > 0) {
                    foreach ($cart_payments as $cart_payment) {
                        $new_cart->first()->cart_payments()->updateExistingPivot($cart_payment['type_payment_id'], $cart_payment);
                    }
                } else {
                    $new_cart->first()->cart_payments()->sync($cart_payments);
                }
                // سعر العنصر الموجود فالسلة
            } else {
                // ارجاع فراغ
                return;
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            /* -------------------------------------------------------------------------- */
            // رسالة نجاح عملية الاضافة:
            return response()->success(
                __("messages.oprations.add_success"),
                $cart->with('cart_items')
                    ->withCount('cart_items')
                    ->isnotbuying()
                    ->first()
            );
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * update => دالة تعديل على عنصر في السلة
     *
     * @param  mixed $id
     * @param  CartRequest $request
     * @return Response
     */
    public function update($id, CartRequest $request)
    {
        try {
            // جلب سلة المستخدم الغير المباعة
            $cart = Cart::where('user_id', Auth::user()->id)->isnotbuying()->first();
            // فحص ان كانت هناك سلة
            if (!$cart) {
                // رسالة خطأ
                return response()->error(__("messages.cart.cart_not_found"), Response::HTTP_NOT_FOUND);
            }
            // جلب سلة مستخدم في حالة تم بيعها
            $cart_found =  Cart::where('user_id', Auth::user()->id)->isnotbuying()->exists();
            // جلب عنصر السلة
            $cart_item_founded = CartItem::whereId($id)
                ->whereCartId($cart->id)
                ->where('product_id', $request->product_id);
            if (!$cart_item_founded->first()) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // شرط في حالة ما اذا قام المستخدم بارسال تطويرات
            if ($request->has('developments')) {
                if ($this->check_found_developments($request->developments, $request->product_id) == 0) {
                    return response()->error(__('messages.cart.same_developments'), Response::HTTP_NOT_FOUND);
                }
            }
            /* ----------------------- التعديل على العنصر من السلة ---------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            //شرط اذا كانت هناك سلة غير مباعة
            if ($cart_found) {
                // جلب عنصر السلة
                $cart_item = $cart_item_founded->with(['product', 'cartItem_developments'])->first();
                // جلب الكمية من المستخدم
                if ($request->has('quantity')) {
                    $cart_item->quantity = (int)$request->quantity <= 0  ? 1 : (int)$request->quantity;
                } else {
                    $cart_item->quantity = 1;
                }
                // حفظ الكمية
                $cart_item->save();
                //شرط اذا كان هناك تطويرات
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $this->add_developments($cart_item, $request->developments);
                }
                $cart = Cart::where('user_id', Auth::user()->id)->isnotbuying();
                // عمليات حساب السعر المتواجد في السلة
                $this->calculate_price($cart, $cart_item, $request->quantity);
            } else {
                // رسالة خطأ
                return response()->error(__('messages.cart.not_found_cartitem'), Response::HTTP_NOT_FOUND);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.update_success"), $cart_item);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => حذف عنصر من السلة
     *
     * @param  mixed $id
     * @return Response
     */
    public function delete(mixed $id)
    {
        try {
            // جلب السلة
            $cart = Cart::where('user_id', Auth::user()->id)
                          ->isnotbuying();
            //id  جلب العنصر بواسطة
            $cart_item = CartItem::whereId($id)
                ->where('cart_id', $cart->first()->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$cart_item || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            DB::beginTransaction();
            // جلب العنصر من السلة
            /* ---------------------------- حذف عنصر من السلة --------------------------- */
            $this->calculate_price($cart, $cart_item, 0);
            // جلب الانواع البوابات المتوفرة
            $type_payments = TypePayment::selection()->get();
            /* ---------------------- // عمليات حسابية على كل بوابة --------------------- */
            $total = $cart->first()['total_price'];
            // اضافة مصفوفة من اجل عمليات الحسابية
            $cart_payments = array_map(function ($key) use ($total) {
                // متغير الرسوم
                $tax = 0;
                // اذا كانت النسبة فوق 1
                if ($key['precent_of_payment'] >= 1) {
                    // اذا كانت الرسوم فوق 1
                    if (($total * $key['precent_of_payment'] / 100) >= 1) {
                        $tax = $total * $key['precent_of_payment'] / 100;
                    } else {
                        $tax = 1;
                    }
                }
                return[
                    'type_payment_id' => $key['id'],
                    'total' => $total,
                    'tax' => $tax + $key['value_of_cent'],
                    'total_with_tax' => $total + $tax + $key['value_of_cent'],
                ];
            }, $type_payments->toArray());

            //  عملية الاضافة
            foreach ($cart_payments as $cart_payment) {
                $cart->first()->cart_payments()->updateExistingPivot($cart_payment['type_payment_id'], $cart_payment);
            }
            // حذف العنصر من السلة
            $cart_item->delete();
            // حذف كل الحسابات المتواجدة في كل بوابة
            if ($cart->first()['total_price'] == 0) {
                $cart->first()->cart_payments()->detach();
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            /* -------------------------------------------------------------------------- */
            // رسالة نجاح عملية الحذف:
            return response()->success(__("messages.oprations.delete_success"), $cart_item);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * calculate_price => دالة تقوم بعملية حساب الاجمالي للسلة مع الرسوم
     *
     * @param  mixed $cart
     * @param  mixed $cart_item
     * @param  mixed $quantity
     * @return void
     */

    private function calculate_price($new_cart, $cart_item, $quantity)
    {
        // سعر العنصر الموجود فالسلة
        $price_cart_item_product = $cart_item['product']->price;
        // سعر تطويرات العنصر الموجود فالسلة
        $price_cart_item_developments = $cart_item['cartItem_developments']->sum('price');
        // تحديث السعر العنصر
        $cart_item->price_product = ($price_cart_item_product + $price_cart_item_developments) * $quantity;
        $cart_item->save();
        // return $new_cart->with('cart_items')->first()['cart_items']->sum('price_product');
        // سعر الكلي
        $total_price = $new_cart->with('cart_items')->first()['cart_items']->sum('price_product');
        // سعر الكلي مع الرسوم
        $price_with_tax = calculate_price_with_tax($new_cart->with('cart_items')->first()['cart_items']->sum('price_product'))['price_with_tax'];
        // سعر الرسوم
        $tax = calculate_price_with_tax($new_cart->with('cart_items')->first()['cart_items']->sum('price_product'))['tax'];
        // تحديث سعر السلة
        $new_cart->update(["total_price" => $total_price, "price_with_tax" => $price_with_tax, "tax" => $tax]);
    }

    /**
     * add_developments => دالة اضافة تطويرات العنصر المتواجد فالسلة
     *
     * @param  object $cart_item
     * @param  Request $developments
     * @return void
     */
    private function add_developments($cart_item, $developments)
    {
        $cart_item->cartItem_developments()->sync(collect($developments));
        $cart_item->load('cartItem_developments');
    }

    /**
     * check_found_developments => دالة تقوم بفحص التطويرات المدخلة من قبل المستخدم
     *
     * @param  mixed $developments
     * @param  mixed $product
     * @return void
     */
    private function check_found_developments($developments, $product)
    {
        // جلب المعرفات التطويرات الخاصة بخدمة معينة من عند المستخدم
        $request_developments = array_map(
            function ($value) {
                return (int)$value;
            },
            $developments
        );

        // جلب المعرفات التطويرات الخاصة بخدمة معينة من قواعد البيانات
        $product_developments = Product::whereId($product)
            ->with('developments')
            ->first()['developments']
            ->pluck('id')
            ->toArray();

        // فحص اذا كانت التطويرات المدخلة لا تطابق بالتطويرات الخدمة
        if (!empty(array_diff($request_developments, $product_developments))) {
            return 0;
        }
        return 1;
    }

    /**
     * cart_approve
     *
     * @return void
     */
    public function cart_approve()
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments', 'product:title')->get();
            },'cart_payments' =>function ($q) {
                $q->select('name_ar', 'name_en', 'status')
                ->where('status', 1)
                ->get();
            } ])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        return $this->approve($cart);
    }

    /**
     * paypal_charge
     *
     * @param  mixed $request
     * @return void
     */
    public function paypal_charge(Request $request)
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments')->get();
            },'cart_payments' =>function ($q) {
                $q->select('name_ar', 'name_en', 'status')
                ->where('status', 1)
                ->get();
            } ])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        return $this->paypal_purchase($request->token, $cart);
    }

    /**
     * stripe_charge
     *
     * @param  mixed $request
     * @return void
     */
    public function stripe_charge(Request $request)
    {
        $cart = Cart::selection()
            ->with(['cart_items' => function ($q) {
                $q->with('cartItem_developments')->get();
            },'cart_payments' =>function ($q) {
                $q->select('name_ar', 'name_en', 'status')
                ->where('status', 1)
                ->get();
            }])
            ->where('user_id', Auth::user()->id)
            ->where('is_buying', 0)
            ->first();
        return $this->stripe_purchase($request, $cart);
    }
}
