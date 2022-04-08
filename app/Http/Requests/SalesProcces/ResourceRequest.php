<?php

namespace App\Http\Requests\SalesProcces;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
            'file_resource' => 'required|file|mimes:zip'
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
            'file_resource.required' => __("messages.validation.file_resource_required"),
            'file_resource.file' => __("messages.validation.file_resource_file"),
            'file_resource.mimes' => __("messages.validation.file_resource_mimes"),
        ];
    }
}
