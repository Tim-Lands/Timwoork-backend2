<?php

namespace App\Http\Requests\withdrawal;

use Illuminate\Foundation\Http\FormRequest;

class PaypalUpdateRequest extends FormRequest
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
            'email' => 'sometimes|email|unique:paypal_accounts,email,'.$this->id,
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
            //'email.required' => __("messages.validation.email_required"),
            'email.email' => __("messages.validation.email"),
            'email.unique' => __("messages.validation.email_unique"),
        ];
    }
}
