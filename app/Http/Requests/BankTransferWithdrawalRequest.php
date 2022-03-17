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
            'amount' => 'numeric',
            'country_id' => 'required',
            'full_name' => 'required',
            'city' => 'required',
            'state' => 'required',
            'phone_number_without_code' => 'required',
            'address_line_one' => 'required',
            'code_postal' => 'required',
            'id_type' => 'required',
            'attachments.*' => 'image|mimes:png,jpg,jpeg|max:2048',

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
            'amount.numeric'      => __("messages.bank.amount_numeric"),
            'country_id.required' => __("messages.validation.country_id"),
            'full_name.required' => __("messages.validation.full_name_required"),
            'city.required' => __("messages.bank.city_required"),
            'state.required' => __("messages.bank.state_required"),
            'phone_number_without_code.required' => __("messages.validation.phone_number_required"),
            'address_line_one.required' => __("messages.bank.address_line_one_required"),
            'code_postal.required' => __("messages.bank.code_postal_required"),
            'id_type.required' => __("messages.bank.id_type_required"),
            'attachments.image' => __("messages.bank.attachments_required"),
            'attachments.max' => __("messages.validation.images_size"),

        ];
    }
}
