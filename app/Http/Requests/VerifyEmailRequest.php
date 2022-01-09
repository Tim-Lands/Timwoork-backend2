<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
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
            'email' => 'required|email',
            'code' => 'required|min:6|max:6'
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
            'code.required' => __("messages.validation.code_required"),
            'code.min' => __("messages.validation.code_size"),
            'code.max' => __("messages.validation.code_size"),
        ];
    }
}
