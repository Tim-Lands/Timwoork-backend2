<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\BanRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserContoller extends Controller
{
    /**
    * get_users => جلب جميع المستخدمين
    *
    * @return void
    */
    public function get_users()
    {
        // جلب جميع المستخدمين
        $users = User::selection()->with('profile')->latest()->get();
        // رسالة نجاح
        return response()->success(__('messages.oprations.get_all_data'), $users);
    }


    /**
     * show => جلب المستخدم الواحد
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب المستخدم الواحد
        $user = User::selection()->whereId($id)->with(['profile','ratings','favorites'])->first();
        // اذا لم يجد المستخدم
        if (!$user) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }

        // رسالة نجاح العملية
        return response()->success(__('messages.oprations.get_data'), $user);
    }


    /**
     * get_user_banned => جلب الأعضاء المحظورين
     *
     * @return void
     */
    public function get_user_banned()
    {
        // جلب المستخدمين المحظورين
        $users_banned = User::selection()->with('bans:bannable_id,comment,expired_at')->onlyBanned()->get();
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $users_banned);
    }

    /**
     * get_user_unbanned => جلب الأعضاء الغير محظورين
     *
     * @return void
     */
    public function get_user_unbanned()
    {
        // جلب المستخدمين الغير المحظورين
        $users_unbanned = User::selection()->withoutBanned()->get();
        // اظهار العناصر
        return response()->success(__('messages.oprations.get_all_data'), $users_unbanned);
    }

    /**
     * user_ban => حظر المستخدم
     *
     * @param  mixed $id
     * @return void
     */
    public function user_ban($id, Request $request)
    {
        try {
            // جلب المستخدم
            $user = User::find($id);
            // فحص المستخدم
            if (!$user) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // حضر من قبل
            if ($user->bans) {
                // رسالة خطأ
                $user->bans()->delete();
            }

            if ($request->expired_at) {
                $data['expired_at'] = $request->expired_at;
            }
            if ($request->comment) {
                $data['comment'] = $request->comment;
            }

            DB::beginTransaction();
            // حظر المستخدم
            $user->ban($data);
            // عمل تسجيل خروج لكل الحسابات
            $user->tokens()->delete();
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.user.ban_success"), $user->load('bans'));
        } catch (Exception $ex) {
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * user_unban => رفع الحظر للمستخدم
     *
     * @param  mixed $id
     * @return void
     */
    public function user_unban($id)
    {
        try {
            // جلب المستخدم
            $user = User::find($id);
            // فحص المستخدم
            if (!$user) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // تحقق من عدم وجود حظر على المستخدم
            if ($user->isNotBanned()) {
                return response()->error(__("messages.user.user_not_banned"), Response::HTTP_FORBIDDEN);
            }

            DB::beginTransaction();
            // حظر المستخدم
            $user->unban();

            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.user.unban_success"), $user);
        } catch (Exception $ex) {
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}