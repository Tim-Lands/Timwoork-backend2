<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    /**
    * messages
    *
    * @return void
    */
    public function messages()
    {
        return [
            'email.required' => __("messages.validation.email_required"),
            'email.email' => __("messages.validation.email"),
            'email.exists' => __("messages.validation.exists"),
            'password.required' => __("messages.validation.password_required"),
            'password.confirmed' => __("messages.validation.password_confirmed"),
            'password_confirmation.required' => __("messages.validation.password_confirmation_required"),
        ];
    }
}
