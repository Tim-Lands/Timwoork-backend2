<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
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
                    return response()->error(__("message.contact.not_found_url"), Response::HTTP_BAD_REQUEST);
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
            return response()->success(__("message.contact.success_message_contact"));
        } catch (Exception $ex) {
            return $ex;
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * sent_to_client_by_email => دالة ارسال الرسالة من لوحة التحكم الى الزبائن بواسطة الايميل
     *
     * @return void
     */
    public function sent_to_client_by_email()
    {
        # code...
    }
}
