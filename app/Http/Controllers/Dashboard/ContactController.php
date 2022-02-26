<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactDashRequest;
use App\Mail\ContactMail;
use App\Models\Contact;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * get_messages_complaints => دالة جلب الشكاوي
     *
     * @return void
     */
    public function get_messages_complaints()
    {
        // جلب الشكاوي
        $messages = Contact::selection()->complaints()->get();
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_all_data"), $messages);
    }

    /**
     * get_messages_enquiries => دالة جلب الاستفسارات
     *
     * @return void
     */
    public function get_messages_enquiries()
    {
        // جلب الاستفسارات
        $messages = Contact::selection()->enquiries()->get();
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_all_data"), $messages);
    }

    /**
     * show => اظهار الرسالة الواحدة
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب الرسالة
        $contact = Contact::find($id);
        // شرط اذا كانت هناك رسالة
        if (!$contact) {
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_BAD_REQUEST);
        }
        // رسالة نجاح العملية
        return response()->success(__("messages.oprations.get_data"), $contact);
    }

    /**
    * sent_to_client_by_email => دالة ارسال الرسالة من لوحة التحكم الى الزبائن بواسطة الايميل
    *
    * @param  mixed $id
    * @param  mixed $request
    * @return void
    */
    public function sent_to_client_by_email($id, ContactDashRequest $request)
    {
        try {
            // جلب الرسالة
            $contact = Contact::find($id);
            // شرط اذا كانت هناك رسالة
            if (!$contact) {
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_BAD_REQUEST);
            }
            // وضع مصفوفة من اجل ارسال رسالة
            $data = [
                'full_name' => $contact->full_name,
                'message' => $request->message,
            ];
            /* ------------------------------- عملية ارسال ------------------------------ */
            // ارسال الرسالة الى الايميل
            Mail::to($contact->email)
                ->send(new ContactMail($data));

            //حالة فشل الرسالة
            if (Mail::failures()) {
                // رسالة خطأ
                return response()->error(__('messages.contact.failures_send_email'), Response::HTTP_BAD_REQUEST);
            }
            // رسالة نجاح العملية
            return response()->success(__("messages.contact.success_send_message_to_email"));
        } catch (Exception $ex) {
            return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
