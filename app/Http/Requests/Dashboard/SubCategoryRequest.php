<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * حدد ما إذا كان المستخدم لديه الاحقية لتقديم هذا الطلب

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
            'name_ar'           => 'required|string',
            'name_en'           => 'required|string',
            'name_fr'           => 'sometimes|nullable',
            'description_ar'    => 'sometimes|string|min:8|max:255',
            'description_en'    => 'sometimes|string|min:8|max:255',
            'description_fr'    => 'sometimes|string|min:8|max:255',
            'parent_id'         => 'required'
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
            'description_ar.min' => __("messages.validation.min_description"),
            'description_en.min' => __("messages.validation.min_description"),
            'description_fr.min' => __("messages.validation.min_description"),
            'parent_id' => __("messages.validation.parent_id")
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
