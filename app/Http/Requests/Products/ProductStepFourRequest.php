<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductStepFourRequest extends FormRequest
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
            'thumbnail'      => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'images'         => 'sometimes',
            'images.*'       => 'mimes:png,jpg,jpeg|max:2048',
            'file'           => 'mimes:pdf|max:2048',
            'url_video'      => 'nullable|url'
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
            'thumbnail.required' =>__("messages.validation.thumbnail"),
            'thumbnail.mimes' =>__("messages.validation.thumbnail_mimes"),
            'thumbnail.size' =>__("messages.validation.thumbnail_size"),
            'images.mimes' => __("messages.validation.images_mimes"),
            'name_en.size' => __("messages.validation.images_size"),
            'file.mimes'   => __("messages.validation.file_mimes"),
            'file.size'   => __("messages.validation.file_size"),
            'url_video.url' => __("messages.validation.url"),
        ];
    }
}
