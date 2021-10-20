<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Get User from users table
        $user = User::where('email', $request->only('username'))
            ->orWhere('username', $request->only('username'))
            ->orWhere('phone', $request->only('username'))
            ->first();
        // if user not found or password is wrong return error message
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'msg' => "invalid credentials"
            ], Response::HTTP_UNAUTHORIZED);
        }
        // if user found loggedin
        Auth::login($user);
        // create token $ store  in cookie with success message
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
