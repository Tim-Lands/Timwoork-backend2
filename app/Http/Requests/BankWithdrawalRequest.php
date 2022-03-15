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

    /**
    * messages
    *
    * @return void
    */
    public function messages()
    {
        return [
            'wise_country_id.required' => __("messages.validation.country_id"),
            'full_name.required' => __("messages.validation.full_name_required"),
            'city.required' => __("messages.bank.city_required"),
            'bank_name.required' => __("messages.bank.bank_name_required"),
            'bank_branch.required' => __("messages.bank.bank_branch_required"),
            'bank_swift.required' => __("messages.bank.bank_swift_required"),
            'bank_iban.required' => __("messages.bank.bank_iban_required"),
            'phone_number_without_code.required' => __("messages.validation.phone_number_required"),
            'address_line_one.required' => __("messages.bank.address_line_one_required"),
            'bank_number_account.required' => __("messages.bank.bank_number_account_required"),
            'bank_adress_line_one.required' => __("messages.bank.bank_adress_line_one_required"),
            'code_postal.required' => __("messages.bank.code_postal_required"),

        ];
    }
}
