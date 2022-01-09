<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required|current_password:sanctum',
            'password' => 'required|confirmed',
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
            'old_password.required' => __("messages.validation.old_password_required"),
            'password.required' => __("messages.validation.password_required"),
            'password.confirmed' => __("messages.validation.password_confirmed"),
            'password_confirmation.required' => __("messages.validation.password_confirmation_required"),
        ];
    }
}
