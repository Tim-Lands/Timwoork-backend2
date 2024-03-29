<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductStepTwoRequest extends FormRequest
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
            'price' => 'required|numeric|between:5,1000',
            'duration' => 'required|gt:0.99',
            'developments.*' => 'sometimes',
            'developments.*.title' => 'required|string|max:255',
            'developments.*.duration' => 'required',
            'developments.*.price' => 'required|numeric|gt:0.99'

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
            'price.required' =>__("messages.validation.price_required"),
            'price.numeric' => __("messages.validation.numeric"),
            'price.between' => __("messages.validation.price_between"),

            'duration.required' => __("messages.validation.duration_required"),
            // developments:
            'developments.*.title.required'    =>__("messages.validation.developements_title_required"),
            'developments.*.title.string'      => __("messages.validation.string"),

            'developments.*.duration.required' => __("messages.validation.developements_duration_required"),

            'developments.*.price.required'    => __("messages.validation.developements_price_required"),
            'developments.*.price.numeric'     => __("messages.validation.numeric"),
            'developments.*.price.gt'          => __("messages.validation.developements_price_great_zero"),
        ];
    }
}
