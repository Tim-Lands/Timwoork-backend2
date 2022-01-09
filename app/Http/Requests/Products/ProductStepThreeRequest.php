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
            'buyer_instruct' => 'required',
            'content' => 'required'
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
            'content.unique' =>__("messages.validation.buyer_instruct_required"),
        ];
    }
}
