<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductStepThreeRequest extends FormRequest
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
            'buyer_instruct' => 'required|min:30',
            'content' => 'required|min:30'
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
            'buyer_instruct.required' =>__("messages.validation.content_required"),
            'buyer_instruct.min' =>__("messages.validation.content_min"),
            'content.required' =>__("messages.validation.buyer_instruct_required"),
            'content.min' =>__("messages.validation.buyer_instruct_min"),
        ];
    }
}
