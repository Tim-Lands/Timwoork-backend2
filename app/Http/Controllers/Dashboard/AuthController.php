<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!auth('admin')->attempt($request->only('email', 'password'))) {
            throw new AuthenticationException();
        }
        return auth('admin')->user();
    }
}
