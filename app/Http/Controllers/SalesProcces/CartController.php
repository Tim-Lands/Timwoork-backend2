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
        $this->middleware('auth:sunctum');
    }

    public function index()
    {
        // عرض السلة المستخدم
        $cart = Cart::selection()->with(['subcarts' => function ($q) {
            $q->with('subcart_developments')->get();
        }])->where('user_id', 3)->where('is_buying', 0)->get();

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
            $cart = Cart::where('user_id', Auth::user()->id)->get();
            // جلب سلة مستخدم في حالة تم بيعها
            $cart_found =  Cart::where('user_id', Auth::user()->id)->where('is_buying', 1)->exists();
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
                $data_sub_cart['cart_id'] = $cart->id;
                // انشاء عنصر جديد
                $sub_cart = SubCart::create($data_sub_cart);
                // شرط في حالة ما اذا كان هناك سعر الاجمالي للخدمة
                if ($request->has('price_product')) {
                    $data_sub_cart['price_product'] = $cart->price_product;
                    $sub_cart->update($data_sub_cart);
                }

                // شرط في حالة ما كانت هناك تطويرات مضافة
                if ($request->has('developments')) {
                    // عملية اضافة تطويرات فالسلة
                    $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
                }

                //في حالة ما اذا وجد المجموع الكلي
                if ($request->has('total_price'))
                    $cart->update(["total_price" => $request->total_price]);
            } else {
                // جلب السلة
                $cart = Cart::where('user_id', Auth::user()->id)->where('is_buying', 0)->first();
                // جلب عنصر السلة بواسطة الخدمة
                $sub_cart = SubCart::where('product_id', $request->product_id)->where('cart_id', $cart->id)->first();
                // وضع معرف السلة في عنصر السلة
                $data_sub_cart['cart_id'] = $cart->id;

                // في حالة ما وجد السعر الاجمالي للخدمة
                if ($request->has('price_product')) {
                    $data_sub_cart['price_product'] = $request->price_product;
                }
                // شرط اذا كان هناك عنصر من قبل من اجل تفادي التكرار
                if ($sub_cart) {
                    // عمل تحديت العنصر
                    $sub_cart->update($data_sub_cart);
                    // شرط اذا كان هناك تطويرات
                    if ($request->has('developments')) {
                        // شرط ادا كان هناك تطويرات من قبل
                        if ($sub_cart->subcart_developments->count())
                            // حذف تطويرات القديمة
                            $sub_cart->subcart_developments()->detach($sub_cart->subcart_developments);
                        // عملية اضافة تطويرات فالسلة
                        $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
                    }
                } else {
                    // انشاء عنصر في السلة
                    $sub_cart = SubCart::create($data_sub_cart);
                    // شرط في حالة ما كانت هناك تطويرات مضافة
                    if ($request->has('developments')) {
                        // عملية اضافة تطويرات فالسلة
                        $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
                    }
                }
                //في حالة ما اذا وجد المجموع الكلي
                if ($request->has('total_price'))
                    // عمل تحديث للخدمة
                    $cart->update(["total_price" => $request->total_price]);
            }
            // انهاء المعاملة بشكل جيد :

            DB::commit();

            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $sub_cart);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
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
            // شرط اذا كان عدد يوجد عنصر واحد من السلة
            if ($sub_cart->count() == 1) {
                // حذف العنصر من السلة
                $sub_cart->delete();
                // حذف السلة كليا
                $sub_cart->cart()->delete();
            } else {
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
}
