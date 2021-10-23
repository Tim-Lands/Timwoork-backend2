<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BadgeRequest extends FormRequest
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
            'name_ar'            => 'required|string|unique:badges,name_ar,' . $this->id,
            'name_en'            => 'sometimes|nullable|unique:categories,name_en,' . $this->id,
            'name_fr'            => 'sometimes|nullable|unique:categories,name_fr,' . $this->id,
            'precent_deducation' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'name_ar.required' => ' اسم المستوى مطلوب',
            'precent_deducation.required' => 'نسبة الاقتطاع مطلوبة',
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
