<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ThumbnailRequest extends FormRequest
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
            'thumbnail'      => 'image|mimes:png,jpg,jpeg|max:2048',
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
            'thumbnail.image' =>__("messages.validation.thumbnail_image"),
            'thumbnail.required' =>__("messages.validation.thumbnail_required"),
            'thumbnail.mimes' =>__("messages.validation.thumbnail_mimes"),
            'thumbnail.size' =>__("messages.validation.thumbnail_size"),
        ];
    }
}
