<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(AdminRequest $request)
    {
        if (!auth('admin')->attempt($request->only('email', 'password'))) {
            return response()->error(__('messages.user.error_login'));
        }
        $user = auth('admin')->user();
        $token = $user->createToken('token')->plainTextToken;
        return response()->success(__('messages.dashboard.get_login'), $token);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
    public function logout()
    {
        return response()->success(__('messages.dashboard.get_logout'));
    }


    /**
     * get_users => جلب جميع المستخدمين
     *
     * @return void
     */
    public function get_users()
    {
        // جلب جميع المستخدمين
        $users = User::selection()->with('profile')->get();
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
}
