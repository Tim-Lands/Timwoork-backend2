<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LevelRequest extends FormRequest
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
            'type' => 'required|integer',
            'name_ar' => 'required',
            'number_developments' => 'required_if:type,=,1|integer',
            'price_developments' => 'required_if:type,=,1',
            'number_sales' => 'required_if:type,=,1',
            'value_bayer' => 'required_if:type,=,0'
        ];
    }

    public function messages()
    {
        return [
            'number_developments.required_if' => 'حقل عدد التطويرات مطلوب',
            'price_developments.required_if' => 'أقصى سعر للتطوير مطلوب',
            'number_sales.required_if' => 'عدد المبيعات المستوى مطلوب',
            'value_bayer.required_if' => 'حقل القيمة الشرائية مطلوب',

        ];
    }
}
