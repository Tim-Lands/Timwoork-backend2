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
            'tags'              => 'required',
            'tags.*'              => 'string|min:3'
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
            'title.required' =>__("messages.validation.title_required"),
            'title.unique' =>__("messages.validation.unique"),
            'title.string' =>__("messages.validation.string"),
            'subcategory.required' =>__("messages.validation.subcategory_required"),
            'subcategory.exists' =>__("messages.validation.exists"),
            'tags.required' => __("messages.validation.tags_required"),
            'tags.*.min' => __("messages.validation.tags_min"),
        ];
    }
}
