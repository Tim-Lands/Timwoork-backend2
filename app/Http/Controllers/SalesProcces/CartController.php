<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\SubCart;
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
        $cart = Cart::selection()->with(['subcarts' => function ($q) {
            $q->with('subcart_developments')->get();
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
            $data_sub_cart = [
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
                $data_sub_cart['cart_id'] = $cart->first()->id;
                // انشاء عنصر جديد
                $sub_cart = SubCart::create($data_sub_cart);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
                    $sub_cart->load('subcart_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $sub_cart, $request->quantity);
            } else {
                $sub_cart_found = SubCart::whereCartId($cart->first()->id)->where('product_id', $request->product_id)->first();
                // return $sub_cart_found;
                // return $cart->with('subcarts')->first()['subcarts']->sum('price_product');
                if ($sub_cart_found)
                    // رسالة خطأ
                    return response()->error('هذا العنصر موجود السلة , اضف عنصر آخر', 403);
                // وضع معرف السلة في مصفوفة العنصر
                $data_sub_cart['cart_id'] = $cart->first()->id;
                // انشاء عنصر جديد
                $sub_cart = SubCart::create($data_sub_cart);
                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
                    $sub_cart->load('subcart_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart->first(), $sub_cart, $request->quantity);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $cart);
        } catch (Exception $ex) {
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
            $sub_cart_founded = SubCart::whereId($id)->whereCartId($cart->id)->where('product_id', $request->product_id);

            if (!$sub_cart_founded->first())
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
                $sub_cart = $sub_cart_founded->with(['product', 'subcart_developments'])->first();
                // حفظ الكمية
                $sub_cart->quantity = $request->quantity;
                $sub_cart->save();
                //شرط اذا كان هناك تطويرات
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $sub_cart->subcart_developments()->sync(collect($request->developments));
                    $sub_cart->load('subcart_developments');
                }
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $sub_cart, $request->quantity);
            } else {
                return response()->error('لا توجد سلة غير مباعة , اضف سلة جديدة من فضلك');
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم تحديث عنصر فالسلة', $sub_cart);
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
            $sub_cart = SubCart::find($id);
            // شرط اذا كان العنصر موجود
            if (!$sub_cart || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود فالسلة', 403);
            // ============= حذف عنصر من السلة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // ============= عملية حذف العنصر من السلة ====================:
            $cart = Cart::whereId($sub_cart->cart_id)->withCount('subcarts')->first();
            if ($cart['subcarts_count'] == 1) {
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $sub_cart, 0);
                // حذف العنصر من السلة
                $sub_cart->delete();
                $cart->delete();
            } else {
                // عمليات حساب السعر المتواجد في السلة 
                $this->calculate_price($cart, $sub_cart, 0);
                // حذف العنصر من السلة
                $sub_cart->delete();
            }


            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الحذف:
            return response()->success('تم حذف عنصر من السلة بنجاح', $sub_cart);
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
     * @param  mixed $sub_cart
     * @param  mixed $quantity
     * @return void
     */
    private function calculate_price($cart, $sub_cart, $quantity)
    {
        // سعر العنصر الموجود فالسلة
        $price_subcart_product = $sub_cart['product']->price;
        // سعر تطويرات العنصر الموجود فالسلة
        $price_subcart_developments = $sub_cart['subcart_developments']->sum('price');

        // تحديث السعر العنصر
        $sub_cart->price_product = ($price_subcart_product + $price_subcart_developments) * $quantity;
        $sub_cart->save();
        // سعر الكلي 
        $cart->total_price = $cart->with('subcarts')->first()['subcarts']->sum('price_product');
        // سعر الكلي مع الرسوم 
        $cart->price_with_tax = calculate_price_with_tax($cart->with('subcarts')->first()['subcarts']->sum('price_product'))['price_with_tax'];
        // سعر الرسوم
        $cart->tax = calculate_price_with_tax($cart->with('subcarts')->first()['subcarts']->sum('price_product'))['tax'];
        // تحديث سعر السلة
        $cart->save();
    }
}
