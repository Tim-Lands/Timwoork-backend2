<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        // عرض السلة المستخدم
        $cart = Cart::selection()->with(['cart_items' => function ($q) {
            $q->with('cartItem_developments')->get();
        }])->where('user_id', Auth::user()->id)->where('is_buying', 0)->get();

        // اظهار السلة مع عناصرها
        return response()->success('عرض سلة المستخدم', $cart);
    }
    /**
     * store
     *
     * @param  mixed $id
     * @param  CartRequest $request
     */
    public function store(CartRequest $request)
    {
        try {
            // جلب سلة المستخدم
            $cart = Cart::where('user_id', Auth::user()->id);
            // جلب سلة مستخدم في حالة تم بيعها
            $cart_found =  Cart::where('user_id', 3)->where('is_buying', 1)->exists();
            // عدد السالات
            $cart_count = $cart->count();
            // وضع البيانات فالمصفوفة من اجل اضافة السلة
            $data_cart = [
                'user_id' => Auth::user()->id,
            ];
            // وضع البيانات فالمصفوفة من اجل اضافة عناصر فالسلة السلة
            $data_cart_items = [
                'product_id'    => $request->product_id,
                'quantity'      => $request->quantity,
            ];
            // شرط في حالة ما اذا قام المستخدم بارسال تطويرات
            if ($request->has('developments')) {
                // جلب المعرفات التطويرات الخاصة بخدمة معينة من عند المستخدم
                $request_developments = array_map(
                    function ($value) {
                        return (int)$value;
                    },
                    $request->developments
                );
                // جلب المعرفات التطويرات الخاصة بخدمة معينة من قواعد البيانات
                $product_developments = Product::whereId($request->product_id)->with('developments')->first()['developments']->pluck('id')->toArray();
                // فحص اذا كانت التطويرات المدخلة لا تطابق بالتطويرات الخدمة
                if (!empty(array_diff($request_developments, $product_developments)))
                    return response()->error('التطويرات التي تم ادخالها ليست مطابقة مع هذه الخدمة');
            }
            // ============= انشاء عنصر جديد فالسلة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة سلة جديدة :
            if ($cart_found || $cart_count == 0) {
                // اضافة سلة جديدة
                $cart = Cart::create($data_cart);
                // وضع معرف السلة في مصفوفة العنصر
                $data_cart_items['cart_id'] = $cart->first()->id;
                // انشاء عنصر جديد
                $cart_item = CartItem::create($data_cart_items);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $cart_item->cartItem_developments()->syncWithoutDetaching(collect($request->developments));
                    $cart_item->load('cartItem_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $cart_item, $request->quantity);
            } else {
                $cart_item_found = CartItem::whereCartId($cart->first()->id)->where('product_id', $request->product_id)->first();
                if ($cart_item_found)
                    // رسالة خطأ
                    return response()->error('هذا العنصر موجود السلة , اضف عنصر آخر', 403);
                // وضع معرف السلة في مصفوفة العنصر
                $data_cart_items['cart_id'] = $cart->first()->id;
                // انشاء عنصر جديد
                $cart_item = CartItem::create($data_cart_items);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $cart_item->cartItem_developments()->syncWithoutDetaching(collect($request->developments));
                    $cart_item->load('cartItem_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart->first(), $cart_item, $request->quantity);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $cart);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            //return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    public function update($id, CartRequest $request)
    {
        try {
            // جلب سلة المستخدم
            $cart = Cart::where('user_id', Auth::user()->id)->first();
            // جلب سلة مستخدم في حالة تم بيعها
            $cart_found =  Cart::where('user_id', Auth::user()->id)->where('is_buying', 0)->exists();
            // جلب عنصر السلة
            $cart_item_founded = CartItem::whereId($id)->whereCartId($cart->id)->where('product_id', $request->product_id);

            if (!$cart_item_founded->first())
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // شرط في حالة ما اذا قام المستخدم بارسال تطويرات
            if ($request->has('developments')) {
                // جلب المعرفات التطويرات الخاصة بخدمة معينة من عند المستخدم
                $request_developments = array_map(
                    function ($value) {
                        return (int)$value;
                    },
                    $request->developments
                );
                // جلب المعرفات التطويرات الخاصة بخدمة معينة من قواعد البيانات
                $product_developments = Product::whereId($request->product_id)->with('developments')->first()['developments']->pluck('id')->toArray();
                // فحص اذا كانت التطويرات المدخلة لا تطابق بالتطويرات الخدمة
                if (!empty(array_diff($request_developments, $product_developments)))
                    return response()->error('التطويرات التي تم ادخالها ليست مطابقة مع هذه الخدمة');
            }

            //شرط اذا كانت هناك سلة غير مباعة
            if ($cart_found) {
                // جلب عنصر السلة
                $cart_item = $cart_item_founded->with(['product', 'cartItem_developments'])->first();
                // حفظ الكمية
                $cart_item->quantity = $request->quantity;
                $cart_item->save();
                //شرط اذا كان هناك تطويرات
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $cart_item->cartItem_developments()->sync(collect($request->developments));
                    $cart_item->load('cartItem_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $cart_item, $request->quantity);
            } else {
                return response()->error('لا توجد سلة غير مباعة , اضف سلة جديدة من فضلك');
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم تحديث عنصر فالسلة', $cart_item);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            //return $ex;
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * delete
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function delete(mixed $id)
    {
        try {

            //id  جلب العنصر بواسطة
            $cart_item = CartItem::find($id);
            // شرط اذا كان العنصر موجود
            if (!$cart_item || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود فالسلة', 403);
            // ============= حذف عنصر من السلة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // ============= عملية حذف العنصر من السلة ====================:
            $cart = Cart::whereId($cart_item->cart_id)->withCount('cart_items')->first();
            if ($cart['cart_items_count'] == 1) {
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $cart_item, 0);
                // حذف العنصر من السلة
                $cart_item->delete();
                $cart->delete();
            } else {
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $cart_item, 0);
                // حذف العنصر من السلة
                $cart_item->delete();
            }


            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الحذف:
            return response()->success('تم حذف عنصر من السلة بنجاح', $cart_item);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * calculate_price
     *
     * @param  mixed $cart
     * @param  mixed $cart_item
     * @param  mixed $quantity
     * @return void
     */
    private function calculate_price($cart, $cart_item, $quantity)
    {
        // سعر العنصر الموجود فالسلة
        $price_cart_item_product = $cart_item['product']->price;
        // سعر تطويرات العنصر الموجود فالسلة
        $price_cart_item_developments = $cart_item['cartItem_developments']->sum('price');

        // تحديث السعر العنصر
        $cart_item->price_product = ($price_cart_item_product + $price_cart_item_developments) * $quantity;
        $cart_item->save();
        // سعر الكلي 
        $cart->total_price = $cart->with('cart_items')->first()['cart_items']->sum('price_product');
        // سعر الكلي مع الرسوم 
        $cart->price_with_tax = calculate_price_with_tax($cart->with('cart_items')->first()['cart_items']->sum('price_product'))['price_with_tax'];
        // سعر الرسوم
        $cart->tax = calculate_price_with_tax($cart->with('cart_items')->first()['cart_items']->sum('price_product'))['tax'];
        // تحديث سعر السلة
        $cart->save();
    }
}
