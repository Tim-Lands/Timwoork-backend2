<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankTransferWithdrawalRequest extends FormRequest
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
            'country_id' => 'required',
            'city' => 'required',
            'state' => 'required',
            'phone_number_without_code' => 'required',
            'address_line_one' => 'required',
            'code_postal' => 'required',
            'id_type' => 'required',
            'attachments' => 'required',
            'attachments.*' => 'image|mimes:png,jpg,jpeg|max:2048',

        ];
    }
}
