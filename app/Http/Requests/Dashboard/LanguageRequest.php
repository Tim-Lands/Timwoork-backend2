<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LanguageRequest extends FormRequest
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
            'name_ar'            => 'required|string|unique:languages,name_ar,' . $this->id,
            'name_en'            => 'sometimes|nullable|unique:languages,name_en,' . $this->id,
            'name_fr'            => 'sometimes|nullable|unique:languages,name_fr,' . $this->id,
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
            'name_ar.required' => __("messages.validation.required_name_ar"),
            'name_ar.unique' => __("messages.validation.unique"),
            'name_en.unique' => __("messages.validation.unique"),
            'name_fr.unique' => __("messages.validation.unique"),
        ];
    }

    /**
     * failedValidation =>  دالة طباعة رسالة الخطأ
     *
     * @param  Validator $validator
     * @return void
     */

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
