<?php

namespace App\Http\Controllers\Auth;

use App\Events\ForgetPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ForgetPasswordResetRequest;
use App\Models\ForgetPasswordToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    public function send_token(ForgetPasswordRequest $request)
    {
        $user = User::whereEmail($request->email)->first();
        $this->store_token($user);
        event(new ForgetPassword($user));
        return response()->success(__("messages.user.send_eamil_reset_password"), ['email' => $user->email]);
    }

    /**
     * Create Token to reset password
     */
    public function store_token($user)
    {
        $token = Str::random(60);
        $forget_token = ForgetPasswordToken::whereEmail($user->email)->first();
        if ($forget_token) {
            $forget_token->delete();
        }
        $forget = ForgetPasswordToken::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token
        ]);
        return $forget;
    }
    public function verify_token(Request $request)
    {
        $verify = ForgetPasswordToken::where('token', $request->token)
            ->first();
        if (!$verify) {
            return response()->error(__("messages.user.error_verify"));
        }
        // إرسال رسالة تفيد بنجاح العملية
        return response()->success(__("messages.user.success_verify_reset_password"), ['email' => $verify->user->email]);
    }

    public function reset_password(ForgetPasswordResetRequest $request)
    {
        $token = ForgetPasswordToken::whereEmail($request->email)
                                      ->where('token', $request->token)
                                      ->first();
        if ($token) {
            $user = $token->user;
            $user->password = bcrypt($request->password);
            if ($user->save()) {
                $token->delete();
            }
            return response()->success(__("messages.user.success_reset_password"));
        } else {
            return response()->error(__("messages.user.fieled_operation"));
        }
    }
}
