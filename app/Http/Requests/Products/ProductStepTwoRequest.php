<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductStepTwoRequest extends FormRequest
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
            'price' => 'required|integer',
            'duration' => 'required',
            'developments.*' => 'sometimes',
            'developments.*.title' => 'required|string|max:255',
            'developments.*.duration' => 'required',
            'developments.*.price' => 'required|integer'

        ];
    }
}
