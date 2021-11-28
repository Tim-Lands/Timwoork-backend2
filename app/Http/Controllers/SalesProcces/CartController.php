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
    // public function __construct()
    // {
    //     $this->middleware('auth:sunctum');
    // }

    public function index()
    {
        $carts = Cart::selection()->with(['cart_developments' => function ($q) {
            $q->select('development_id')->get();
        }])->where('user_id', Auth::user()->id)->get();




        // اظهار العناصر
        return response()->success('عرض سلة المستخدم', $carts);
    }
    /**
     * store
     *
     * @param  mixed $id
     * @param  CartRequest $request
     * @return JsonResponse
     */
    public function store(CartRequest $request)
    {
        try {
            $cart = Cart::where('user_id', $request->user_id);

            $cart_found = $cart->where('is_buying', 1)->exists();

            $cart_count = $cart->count();
            // وضع البيانات فالمصفوفة من اجل اضافة عنصر فالسلة
            $data_cart = [
                'user_id' => $request->user_id,
            ];

            $data_sub_cart = [
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ];

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
            DB::beginTransaction();
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :

            // عملية اضافة سلة جديدة :
            $cart = Cart::create($data_cart);

            // if ($cart_found || $cart_count == 0) {

            //     // $data['cart_id'] = $cart->id;
            //     // $sub_cart = SubCart::create($data_sub_cart);

            //     // // شرط في حالة ما كانت هناك تطويرات مضافة
            //     // if ($request->has('developments')) {
            //     //     // عملية اضافة تطويرات فالسلة
            //     //     $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
            //     // }
            //     // // انهاء المعاملة بشكل جيد :
            // } else {
            //     $cart = Cart::where('user_id', $request->user_id)->where('is_buying', 0)->first();
            //     // $data_sub_cart['cart_id'] = $cart->id;

            //     // $sub_cart = SubCart::create($data_sub_cart);
            //     // // شرط في حالة ما كانت هناك تطويرات مضافة
            //     // if ($request->has('developments')) {
            //     //     // عملية اضافة تطويرات فالسلة
            //     //     $sub_cart->subcart_developments()->syncWithoutDetaching(collect($request->developments));
            //     // }
            //     // انهاء المعاملة بشكل جيد :
            // }
            DB::commit();

            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $cart);
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
            $cart = Cart::find($id);
            // شرط اذا كان العنصر موجود
            if (!$cart || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // ============= حذف عنصر من السلة  ================:

            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف العنصر من السلة :
            $cart->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الحذف:
            return response()->success('تم حذف عنصر من السلة بنجاح', $cart);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
