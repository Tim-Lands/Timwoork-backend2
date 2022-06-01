<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class TypePaymentRequest extends FormRequest
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
            "name_ar" => "required",
            "name_en" => "required",
            "precent_of_payment" => "required|numeric|between:0,100",
            "value_of_cent" => "required|numeric",
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
            'name_ar.required' =>__("messages.validation.required_name_ar"),
            'name_en.required' =>__("messages.validation.name_en_required"),
            'precent_of_payment.required' => __("messages.type_payment.precent_of_payment_required"),
            'precent_of_payment.numeric' => __("messages.type_payment.precent_of_payment_numeric"),
            'precent_of_payment.between' => __("messages.type_payment.precent_of_payment_between"),
            'value_of_cent.required' => __("messages.type_payment.value_of_cent_required"),
            'value_of_cent.numeric' => __("messages.type_payment.value_of_cent_numeric"),

        ];
    }
}
