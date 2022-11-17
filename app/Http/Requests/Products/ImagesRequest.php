<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ImagesRequest extends FormRequest
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
            'images'         => 'required',
            'images.*'       => 'image|mimes:png,jpg,jpeg|max:2048',
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
            "images.required" =>__("messages.product.galaries_required"),
            'images.*.image' => __("messages.validation.images_mimes"),
            'images.*.mimes' => __("messages.validation.images_mimes"),
            'images.*.max' => __("messages.validation.images_size"),
        ];
    }
}
