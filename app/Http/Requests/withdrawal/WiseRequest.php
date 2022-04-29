<?php

namespace App\Http\Requests\withdrawal;

use Illuminate\Foundation\Http\FormRequest;

class WiseRequest extends FormRequest
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
            'email' => 'required|email|unique:wise_accounts,email,'.$this->id,
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
            'unique.required' => __("messages.validation.unique"),
        ];
    }
}
