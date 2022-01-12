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
            'price' => 'required|integer|between:5,5000',
            'duration' => 'required',
            'developments.*' => 'sometimes',
            'developments.*.title' => 'required|string|max:255',
            'developments.*.duration' => 'required',
            'developments.*.price' => 'required|integer'

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
            'price.integer' => __("messages.validation.numeric"),
            'price.between' => __("messages.validation.price_between"),
            'duration.required' => __("messages.validation.duration_required"),

            'developments.*.duration.required' =>__("messages.validation.developements_title_required"),
            'developments.*.duration.string' => __("messages.validation.string"),

            'developments.*.duration.required' => __("messages.validation.developements_duration_required"),
            'developments.*.price.required' => __("messages.validation.developements_price_required"),
            'developments.*.price.integer' => __("messages.validation.numeric")
        ];
    }
}
