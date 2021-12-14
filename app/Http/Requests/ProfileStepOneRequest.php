<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepOneRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => ['required', Rule::unique('users', 'username')->ignore($this->profile->user), 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'date_of_birth' => 'required|date_format:Y-m-d',
            'gender' => 'required',
            'country_id' => 'required',
        ];
    }
}
