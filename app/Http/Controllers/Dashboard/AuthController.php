<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

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
}
