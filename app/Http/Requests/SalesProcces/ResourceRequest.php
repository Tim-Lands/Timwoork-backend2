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
            'file_resource.required' => 'هذا الحقل موجود من قبل',
            'file_resource.file' => 'هذا الحقل موجود من قبل',
            'file_resource.mimes' => 'هذا الحقل موجود من قبل',
        ];
    }
}
