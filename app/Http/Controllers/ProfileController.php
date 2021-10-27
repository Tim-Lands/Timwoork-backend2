<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStepOneRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function show($username)
    {
        // البحث في قاعدة البيانات عن اسم المستخدم
        $user = User::where('username', $username)
            ->orWhere('id', $username)
            ->get();
        if ($user->isEmpty()) {
            // في حالة عدم وجود اسم مستخدم يتم إرسال رسالة الخطأ
            return response()->error('عذرا لم نجد معلومات مطابقة لهذا الاسم');
        } else {
            // في حالة وجود اسم المستخدم يتم عرض معلوماته الشخصية
            return response()->success('لقد تمّ جلب معلومات الملف الشخصي', $user);
        }
    }

    public function step_one(ProfileStepOneRequest $request)
    {
    }
}
