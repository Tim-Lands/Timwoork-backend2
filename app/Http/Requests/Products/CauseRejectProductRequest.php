<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CauseRejectProductRequest extends FormRequest
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
            'cause' => 'required',
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
            "cause.required" =>__("messages.product.cause_required"),
        ];
    }
}
