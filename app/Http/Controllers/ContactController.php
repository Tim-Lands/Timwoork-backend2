<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactDashRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Models\Contact;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
     * send_to_dashboad => دالة ارسال الرسالة الى لوحة التحكم
     *
     * @param  mixed $request
     * @return void
     */
    public function send_to_dashboad(ContactRequest $request)
    {
        try {
            $contact = Contact::selection()
                                        ->where(function ($query) use ($request) {
                                            $query->where('email', $request->email)
                                                  ->orWhere('ip_client', $request->ip());
                                        })
                                        ->where('date_expired', '>', Carbon::now()->toDateTimeString())
                                        ->first();
            if ($contact) {
                return response()->error(__("messages.contact.cannot_sent_before_48"), Response::HTTP_BAD_REQUEST);
            }
            // انشاء مصفوفة من اجل ارسال المعلومات
            $data_contact = [
                "subject"         => $request->subject,
                "email"           => $request->email,
                "full_name"       => $request->full_name,
                "type_message"    => $request->type_message,
                "message"         => $request->message,
                "date_expired"    => Carbon::now()->addDays(2)->toDateTimeString(),
                "ip_client"       => $request->ip()
            ];

            // شرط اذا كان يوجد رابط
            if ($request->has('url')) {
                // شرط ان يكون الرابط يحتوي على غوغل درايف او دروب بوكس
                if (str_contains($request->url, Contact::URL_GOOGLE_DRIVE) || str_contains($request->url, Contact::URL_DROPBOX)) {
                    $data_contact["url"] = $request->url;
                } else {
                    // رسالة خطأ
                    return response()->error(__("messages.contact.not_found_url"), Response::HTTP_BAD_REQUEST);
                }
            }
            /* ------------------------------- عملية ارسال ------------------------------ */

            DB::beginTransaction();
            // عملية انشاء الرسالة
            Contact::create($data_contact);
            // ارسال اشعار للوحة التحكم
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            // رسالة نجاح العملية
            return response()->success(__("messages.contact.success_message_contact"));
        } catch (Exception $ex) {
            return $ex;
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
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
