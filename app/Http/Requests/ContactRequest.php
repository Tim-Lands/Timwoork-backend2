<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            "subject"        => "required",
            "email"          => "required|email",
            "full_name"      => "required",
            "type_message"   => "required",
            "message"        => "required",
            "url"            => "nullable|url",  // 5 MB
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
            'subject.required' => __("messages.validation.subject_required"),
            'email.required' => __("messages.validation.email_required"),
            'email.email' => __("messages.validation.email"),
            'full_name.required' => __("messages.validation.full_name_required"),
            'message.required' => __("messages.validation.message_required"),
            'type_message.required' => __("messages.validation.type_message_required"),
            'url.url' => __("messages.validation.url"),

        ];
    }
}
