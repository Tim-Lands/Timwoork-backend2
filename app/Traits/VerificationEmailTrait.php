<?php

namespace App\Traits;

use App\Events\VerifyEmail;
use App\Models\User;
use App\Models\VerifyEmailCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Response;
//use Illuminate\Support\Facades\RateLimiter;

//use Illuminate\Support\Str;

trait VerificationEmailTrait
{
    public function generateVerificationCode($length = 6)
    {
        // إنشاء كود التفعيل المكوّن من 6 أرقام ويمكن تغيير طول الرقم باختيار الطول، في حالة عدم اختيار الطول، الطول الافتراضي للكود هو 6
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }
        return $code;
    }

    public function store_code_bin($user)
    {
        // تخزين الكود في جدول مع البريد الالكتروني ومعرف المستخدم
        return $verify = VerifyEmailCode::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $this->generateVerificationCode(),
            //'date_expired' => Carbon::now()->addHour(1)
        ]);
    }

    public function verify_email($email, $code)
    {
        try{
        // تفعيل البريد الالكتروني بادخال البريد الالكتروني مع رمز التفعيل
        $verify = VerifyEmailCode::where('email', $email)
            ->where('code', $code)
            //->where('date_expired', '<=', Carbon::now())
            ->first();
        if (!$verify) {
            return response()->error('حدث خطأ ما لم يتم العثور على رمز التفعيل الخاص بك');
        }
        // التأكد من أن البريد الالكتروني مفعّل ام لا

        if (!$verify->user->email_verified_at) {
            // حالة أنه غير مفعّل

            // يتم التعديل على حقل email_verified_at
            // ثم يتم حذف الرمز من قاعدة البيانات لعدم الحاجة إليه
            $verify->user->email_verified_at = Carbon::now();
            $verify->user->save();
            $verify->delete();

            // إرسال رسالة تفيد بنجاح العملية
            return response()->success('لقد تم تفعيل حسابك بنجاح');
        } else {
            // حالة البريد الالكتروني مفعّل
            //  يتم ارسال رسال مفادها أن البريد الالكتروني الذي تريد تفعيله، مفعّل سابقا

            return response()->error('البريد اﻹلكتروني مفعّل من قبل');
        }
    }
    catch (Exception $exc){
        echo $exc;
    }
    }

    public function resend_code($email)
    {
        // فحص عدد ارسال مرات كود التفعيل
        // استخراج رمز التفعيل من قاعدة البيانات باستعمال البريد الالكتروني
        $verify = VerifyEmailCode::where('email', $email)
                //->where('date_expired', '>=', Carbon::now())
                ->first();

            // في  حالة عدم وجود رمز التفعيل يتم البحث عن البريد الالكتروني هل هو موجود في قاعدة البيانات ام لا
            // في حالة عدم وجوده يتم إرسال رسالة خطأ بعدم وجود الايميل في سجلاتنا
            $user = User::where('email',$email)->firstOrFail();
            //  في حالة وجود مستخدم مسجل بالبريد الالكتروني يتم إنشاء  رمز تفعيل جديد له
            $this->store_code_bin($user);
            // بعد إنشاء رمز التفعيل الجديد يتم إرساله
            event(new VerifyEmail($user));
            // عداد الارسال الكود
            //RateLimiter::hit($this->throttleKey(), $seconds = 60);
            // مع إرسال رسالة نجاح العملية
            return $this->success('تم إرسال رمز التفعيل بنجاح إلى بريدك اﻹلكتروني');

    }


}
