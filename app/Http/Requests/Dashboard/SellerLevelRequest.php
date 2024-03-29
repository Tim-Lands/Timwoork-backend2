<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SellerLevelRequest extends FormRequest
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
         * قواعد التحقق التي تنطبق على الطلب

         * @return array
         */
    public function rules()
    {
        return [
            'name_ar'             => 'required|string|unique:seller_levels,name_ar,' . $this->id,
            'name_en'             => 'sometimes|nullable|unique:seller_levels,name_en,' . $this->id,
            'name_fr'             => 'sometimes|nullable|unique:seller_levels,name_fr,' . $this->id,
            'type'                => 'required',
            'number_developments' => 'required_if:type,=,1|integer',
            'price_developments'  => 'required_if:type,=,1',
            'number_sales'        => 'required_if:type,=,1',
            'value_bayer'         => 'required_if:type,=,0'
        ];
    }

    public function messages()
    {
        return [
            'name_ar.required' =>__("messages.validation.required_name_ar"),
            'type.required' => __("messages.validation.type_level"),
            'number_developments.required_if' => __("messages.validation.number_developments"),
            'price_developments.required_if' => __("messages.validation.price_developments"),
            'number_sales.required_if' => __("messages.validation.number_sales"),
            'value_bayer.required_if' => __("messages.validation.value_bayer"),

        ];
    }
}
