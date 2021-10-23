<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
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
            'name_ar' => 'required',
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
}
