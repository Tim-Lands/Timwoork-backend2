<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:users',
            'username' => ['required', "unique:users,username," . Auth::id(), 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()],
            'phone' => ['required', 'numeric',  'digits_between:4,16'],
            'code_phone' => ['required']
            //'phone' => ['required', 'unique:users,phone,' . Auth::id(), 'min:8', 'max:12'],
            // phone required, unique, digits, min:8, max:12
            //'phone' => ['required', 'unique:users,phone,' . Auth::id(),'numeric'],
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
            'email.unique' => __("messages.validation.unique"),
            'username.required' => __("messages.validation.username_required"),
            'username.unique' => __("messages.validation.unique"),
            'password.required' => __("messages.validation.password_required"),
            'password.confirmed' => __("messages.validation.password_confirmed"),
            'phone.required' => __("messages.validation.phone_number_required"),
            //'phone.unique' => __("messages.validation.phone_unique"),
            'phone.numeric' => __("messages.validation.phone_number_numeric"),
            'phone.digits_between' => __("messages.validation.phone_digits_between"),
            'code_phone.required' => __('messages.validation.code_phone_required')

        ];
    }
}
