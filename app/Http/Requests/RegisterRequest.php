<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'password' => 'required|min:8'
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
            'phone_number.required' => __("messages.validation.phone_number_required"),
        ];
    }
}
