<?php

namespace App\Http\Requests\SalesProcces;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
            'quantity'    => 'sometimes|numeric',
            'product_id'  => 'required',
            'developments.*' => 'sometimes|exists:developments,id',
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
            'quantity.numeric' => __("messages.validation.numeric"),
            'product_id.required' => __("messages.validation.product_id"),
            'developments.exists' => __("messages.validation.exists"),
        ];
    }
}
