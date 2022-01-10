<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        $dt1 = new Carbon();
        $dt2 = new Carbon();
        $before = $dt1->now()->subYears(19)->format('Y-m-d');
        $after = $dt2->subYears(101)->format('Y-m-d');
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required|date_format:Y-m-d|before_or_equal:' . $before . '|after_or_equal:' . $after,
            'gender' => 'required',
            'country_id' => 'required',
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
            'first_name.required' => __("messages.validation.bio_required"),
            'last_name.min' => __("messages.validation.bio_min"),
            'username.required' => __("messages.validation.username_required"),
            'username.unique' => __("messages.validation.unique"),
            'date_of_birth.required' => __("messages.validation.date_of_birth_required"),
            'gender.required' => __("messages.validation.gender_required"),
            'country_id.required' => __("messages.validation.country_id"),
        ];
    }
}
