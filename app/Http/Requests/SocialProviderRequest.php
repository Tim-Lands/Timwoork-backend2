<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialProviderRequest extends FormRequest
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
            'provider_id' => 'required',
            'email' => 'required',
            'full_name' => 'required',
            'username' => 'required'
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
            'provider_id.required' => __("messages.validation.provider_id"),
            'email.required' => __("messages.validation.email_required"),
            'full_name.required' => __("messages.validation.skills"),
        ];
    }
}
