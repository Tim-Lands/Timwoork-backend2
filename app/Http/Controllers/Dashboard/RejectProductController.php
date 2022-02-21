<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RejectProductRequest;
use App\Mail\RejectProduct;
use App\Models\RejectProduct as ModelReject;
use App\Models\Product;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RejectProductController extends Controller
{
    public function __invoke($id, RejectProductRequest $request)
    {
        try {

            // جلب الخدمة المرفوضة
            $product = Product::select('id', 'title', 'status', 'is_completed', 'price', 'profile_seller_id')
                                           ->whereId($id)
                                           ->where('is_completed', 1)
                                           ->productreject()
                                           ->with(['ProfileSeller' => function ($q) {
                                               $q->select('id', 'profile_id')->with('profile', function ($q) {
                                                   $q->select('id', 'first_name', 'last_name', 'user_id')
                                                   ->with('user:id,email')->without(['level','badge']);
                                               })->without(['level','badge']);
                                           }])->first();

            // شرط اذا كانت الخدمة المرفوضة غير موجودة
            if (!$product) {
                // ارسال خطأ
                return response()->error(__('messages.errors.element_not_found'), Response::HTTP_NOT_FOUND);
            }
            // وضع متغيرات من اجل ارسال رسالة الرفض
            $data_reject_product = [
                'title_product' => $product->title,
                'first_name' => $product->profileSeller->profile->first_name,
                'last_name'  => $product->profileSeller->profile->last_name,
                'email'     => $product->profileSeller->profile->user->email,
                'message_rejected' => $request->message_rejected,
                'product_id' => $product->id
            ];
            /* ------------------------------- عملية ارسال ------------------------------ */
            // ارسال الرسالة الى الايميل
            Mail::to($data_reject_product['email'])
                    ->send(new RejectProduct($data_reject_product));

            //حالة فشل ارسال الرسالة
            if (!Mail::failures()) {
                /* ---------------------- عملية اضافة في قواعد البيانات --------------------- */
                // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
                DB::beginTransaction();
                // عملية الاضافة
                ModelReject::create($data_reject_product);
                // انهاء المعاملة بشكل جيد :
                DB::commit();
                // رسالة نجاح العملية
                return response()->success(__("messages.dashboard.success_send_message_to_email"));
            } else {
                // رسالة خطأ
                return response()->error(__('messages.dashboard.failures_send_email'), Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
