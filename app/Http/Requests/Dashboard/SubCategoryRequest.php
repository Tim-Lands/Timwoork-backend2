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
            'name_ar'           => 'required|string|unique:categories,name_ar,' . $this->id,
            'name_en'           => 'required|string|unique:categories,name_en,' . $this->id,
            'name_fr'           => 'sometimes|nullable|unique:categories,name_fr,' . $this->id,
            'description_ar'    => 'sometimes|string|min:8|max:255',
            'description_en'    => 'sometimes|string|min:8|max:255',
            'description_fr'    => 'sometimes|string|min:8|max:255',
            'icon'              => 'required',
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
            'name_ar.required' => ' اسم المستوى مطلوب',
            'name_ar.unique' => 'هذا الحقل موجود من قبل',
            'name_en.unique' => 'هذا الحقل موجود من قبل',
            'name_fr.unique' => 'هذا الحقل موجود من قبل',
            'description_ar.min' => 'هذا الحقل موجود من قبل',
            'description_en.min' => 'هذا الحقل موجود من قبل',
            'description_fr.min' => 'هذا الحقل موجود من قبل',
            'icon.required' => 'نسبة الاقتطاع مطلوبة',
            'parent_id' => 'هذا الحقل مطلوب'
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
