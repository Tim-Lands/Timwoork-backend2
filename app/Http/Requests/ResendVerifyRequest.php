<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendVerifyRequest extends FormRequest
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
            'email' => 'required|email'
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
