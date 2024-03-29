<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SellerBadgeRequest extends FormRequest
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
            'name_ar'            => 'required|string|unique:seller_badges,name_ar,' . $this->id,
            'name_en'            => 'sometimes|nullable|unique:seller_badges,name_en,' . $this->id,
            'name_fr'            => 'sometimes|nullable|unique:seller_badges,name_fr,' . $this->id,
            'precent_deducation' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'name_ar.required' =>__("messages.validation.required_name_ar"),
            'name_ar.unique' => __("messages.validation.unique"),
            'name_en.unique' => __("messages.validation.unique"),
            'name_fr.unique' => __("messages.validation.unique"),
            'precent_deducation.required' => __("messages.validation.required_precent_deducation"),

        ];
    }
}
