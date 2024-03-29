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
            'title'               => 'required|string|min:20|max:60',
            'subcategory'         => 'required|exists:categories,id',
            'tags'              => 'required',
            'tags.*.value'        => 'required',
            'is_tutorial' => 'nullable|boolean'
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
            'title.required' => __("messages.validation.title_required"),
            'title.min' => __("messages.validation.title_size"),
            'title.max' => __("messages.validation.title_size"),
            'title.string' => __("messages.validation.string"),
            'subcategory.required' => __("messages.validation.subcategory_required"),
            'subcategory.exists' => __("messages.validation.exists"),
            'tags.required'    => __("messages.validation.tags_required"),
            'tags.*.value.required'    => __("messages.validation.tags_value_required"),
            'is_tutorial.boolean'=>__("messages.validation.is_tutorial_boolean")
        ];
    }
}
