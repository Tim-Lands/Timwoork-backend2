<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name_ar'  => 'required|unique:countries,name_ar,' . $this->id,
            'name_en'  => 'sometimes|nullable|unique:countries,name_en,' . $this->id,
            'name_fr'  => 'sometimes|nullable|unique:countries,name_fr,' . $this->id,
            'code_phone' => 'sometimes|nullable|unique:countries,code_phone,' . $this->id,
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
            'name_ar.unique' => __("messages.validation.unique"),
            'name_en.unique' => __("messages.validation.unique"),
            'name_fr.unique' => __("messages.validation.unique"),
            'code_phone.unique' => __("messages.validation.unique"),
        ];
    }
}
