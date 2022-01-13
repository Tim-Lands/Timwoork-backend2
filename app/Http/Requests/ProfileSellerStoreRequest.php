<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSellerStoreRequest extends FormRequest
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
            'bio' => 'min:26',
            'portfolio' => 'url',
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
            //'portfolio.required' => __("messages.validation.portfolio_required"),
            'portfolio.url' => __("messages.validation.url"),
            //'skills.required' => __("messages.validation.skills_required"),
        ];
    }
}
