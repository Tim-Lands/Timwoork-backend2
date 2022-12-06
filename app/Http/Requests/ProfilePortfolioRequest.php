<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePortfolioRequest extends FormRequest
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
            'portfolio' => 'required|url',
            "cover" => 'required'
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
            'portfolio.required' => __("messages.validation.portfolio_required"),
            'portfolio.url'=>__("messages.validation.url"),
            'cover.required' => __("messages.validation.cover_required"),
        ];
    }
}
