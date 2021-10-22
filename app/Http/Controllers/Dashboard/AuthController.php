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
            return response()->json([
                'msg' => "invalid credentials"
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = auth('admin')->user();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('timwoork_token', $token, 6 * 24);
        return response([
            'msg' => "Success"
        ])->withCookie($cookie);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
    public function logout()
    {
        $cookie = cookie()->forget('timwoork_token');
        return response([
            'msg' => "Success"
        ])->withCookie($cookie);
    }
}
