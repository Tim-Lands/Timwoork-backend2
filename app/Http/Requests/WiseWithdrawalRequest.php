<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WiseWithdrawalRequest extends FormRequest
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
            'email' => 'required|email|unique:wise_accounts,email,' . Auth::id()
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
            'email.required' => __("messages.validation.email_required"),
            'email.email' => __("messages.validation.email"),
            'unique.required' => __("messages.validation.unique"),
        ];
    }
}
