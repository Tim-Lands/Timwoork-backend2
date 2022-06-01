<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConversationStoreRequest extends FormRequest
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
            'title' => 'required',
            'receiver_id' => 'required',
            'initial_message' => 'required'
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
            'title.required' => __("messages.validation.conversation_title"),
            'receiver_id.required' => __("messages.validation.receiver_id"),
            'initial_message.required' => __("messages.validation.initial_message"),
        ];
    }
}
