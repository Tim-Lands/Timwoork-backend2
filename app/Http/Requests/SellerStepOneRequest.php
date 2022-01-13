<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerStepOneRequest extends FormRequest
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
            'bio' => 'sometimes|min:26',
            'languages.*' => 'required'
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
            //'bio.required' => __("messages.validation.bio_required"),
            'bio.min' => __("messages.validation.bio_min"),
            'languages.required' => __("messages.validation.languages"),
        ];
    }
}
