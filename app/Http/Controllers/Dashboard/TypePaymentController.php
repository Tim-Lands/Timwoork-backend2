<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TypePaymentRequest;
use App\Models\TypePayment;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TypePaymentController extends Controller
{
    /**
     * index => عرض جميع البوابات
     *
     * @return void
     */
    public function index()
    {
        $types_payments = TypePayment::selection()->get();

        return response()->success(__("messages.oprations.get_all_data"), $types_payments);
    }

    /**
     * show => جلب البوابة
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب البوابة
        $type_payment = TypePayment::selection()->whereId($id)->first();
        // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
        if (!$type_payment || !is_numeric($id)) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $type_payment);
    }

    /**
     * store => اضافة بوابة جديدة
     *
     * @param  mixed $request
     * @return void
     */
    public function store(TypePaymentRequest $request)
    {
        try {
            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'               => $request->name_ar,
                'name_en'               => $request->name_en,
                'precent_of_payment'    => $request->precent_of_payment,
                'value_of_cent'          => $request->value_of_cent,
            ];
            // ============= انشاء انشاء بوابة جديدة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية اضافة بوابة :
            $type_payment = TypePayment::create($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.add_success"), $type_payment);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"));
        }
    }

    /**
     * update => تعديل على البوابة
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function update($id, TypePaymentRequest $request)
    {
        try {
            //من اجل التعديل  id  جلب العنصر بواسطة المعرف
            $type_payment = TypePayment::find($id);

            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$type_payment || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // جلب البيانات و وضعها في مصفوفة:
            $data = [
                'name_ar'               => $request->name_ar,
                'name_en'               => $request->name_en,
                'precent_of_payment'    => $request->precent_of_payment,
                'value_of_cent'          => $request->value_of_cent,
            ];

            // ============= التعديل على نوع البوابة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية التعديل على نوع البوابة :
            $type_payment->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية التعديل:
            return response()->success(__("messages.oprations.update_success"), $type_payment);
        } catch (Exception $ex) {
            // رسالة خطأ :
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * delete => حذف البوابة
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            //من اجل الحذف  id  جلب العنصر بواسطة المعرف
            $type_payment = TypePayment::find($id);
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$type_payment || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }

            // ============= حذف البوابة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف البوابة :
            $type_payment->delete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================

            // رسالة نجاح عملية الحذف:
            return response()->success(__('messages.oprations.delete_success'));
        } catch (Exception $ex) {
            DB::rollBack();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * active_payment => تنشيط البوابة
     *
     * @param  mixed $id
     * @return void
     */
    public function active_payment($id)
    {
        try {
            // جلب البوابة الغير النشطة
            $type_payment = TypePayment::whereId($id)->PaymentDisactive()->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$type_payment || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // ============= تنشيط البوابة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية تنشيط البوابة :
            $type_payment->update(['status' => 1]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الحذف:
            return response()->success(__('messages.type_payment.active_payment'));
        } catch (Exception $ex) {
            DB::rollBack();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * disactive_payment => الغاء تنشيط البوابة
     * @param  mixed $id
     * @return void
     */
    public function disactive_payment($id)
    {
        try {
            // جلب البوابة النشطة
            $type_payment = TypePayment::whereId($id)->PaymentActive()->first();
            // شرط اذا كان العنصر موجود او المعرف اذا كان رقم غير صحيح
            if (!$type_payment || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // ============= الغاء تنشيط البوابة  ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية الغاء تنشيط البوابة :
            $type_payment->update(['status' => 0]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // =================================================
            // رسالة نجاح عملية الحذف:
            return response()->success(__('messages.type_payment.disactive_payment'));
        } catch (Exception $ex) {
            DB::rollBack();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
