<?php

namespace App\Http\Requests\products;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionInfoRequest extends FormRequest
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
            'sessions'=>'required',
            'sessions.*.repeating_type' => 'sometimes|in:DAILY,WEEKLY,MONTHLY',
            'sessions.*.session_date' => 'required|date'
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
            'sessions.*.repeating_type.in'=>__("messages.validation.repeating_type_in"),

        ];
    }
}
