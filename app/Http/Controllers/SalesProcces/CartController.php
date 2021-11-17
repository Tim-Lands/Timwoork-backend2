<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\CartRequest;
use App\Models\Cart;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::selection()->with(['cart_developments' => function ($q) {
            $q->select('development_id')->get();
        }])->where('user_id', 2)->get();

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
            // وضع البيانات فالمصفوفة من اجل اضافة عنصر فالسلة
            $data = [
                'user_id'    =>
                /** Auth::user()->id */
                2,
                'product_id' => (int)$request->product_id,
                'quantity'   => (int)$request->quantity
            ];
            // ============= انشاء عنصر جديد فالسلة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة تصنيف :
            $cart = Cart::create($data);
            // شرط في حالة ما كانت هناك تطويرات مضافة
            if ($request->has('developments')) {
                // عملية اضافة تطويرات فالسلة
                $cart->cart_developments()->syncWithoutDetaching(collect($request->developments));
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء عنصر فالسلة', $cart);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
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
