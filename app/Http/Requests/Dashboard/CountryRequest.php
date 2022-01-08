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
            'name_ar.required' => ' اسم المستوى مطلوب',
            'name_ar.unique' => 'هذا الحقل موجود من قبل',
            'name_en.unique' => 'هذا الحقل موجود من قبل',
            'name_fr.unique' => 'هذا الحقل موجود من قبل',
            'code_phone.unique' => 'هذا الحقل موجود من قبل',
        ];
    }
}
