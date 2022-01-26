<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;

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

    public function me(AdminRequest $request)
    {
        return $request->user();
    }
    public function logout()
    {
        return response()->success(__('messages.dashboard.get_logout'));
    }
}
