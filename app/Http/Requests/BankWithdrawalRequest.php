<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankWithdrawalRequest extends FormRequest
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
            'wise_country_id' => 'required',
            'full_name' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'bank_adress_line_one' => 'required',
            'bank_swift' => 'required',
            'bank_iban' => 'required',
            'bank_number_account' => 'required',
            'phone_number_without_code' => 'required',
            'city' => 'required',
            'address_line_one' => 'required',
            'code_postal' => 'required',
        ];
    }
}
