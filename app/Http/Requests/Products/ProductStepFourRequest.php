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
            'thumbnail'      => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
            //'images'         => 'sometimes',
            //'images.*'       => 'mimes:png,jpg,jpeg|max:2048',
            //'file'           => 'mimes:pdf|max:2048',
            //'url_video'      => 'nullable|url'
        ];
    }
}
