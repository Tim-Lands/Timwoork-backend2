<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductStepOneRequest extends FormRequest
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
            'title'               => 'required|string|max:255|unique:products,title,' . $this->id,
            'subcategory'         => 'required|exists:categories,id',
            'tags.*'              => 'required|exists:tags,id'
        ];
    }
}
