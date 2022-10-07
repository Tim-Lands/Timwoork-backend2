<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMeProducts extends FormRequest
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


    protected function prepareForValidation() 
    {
        echo "prepaaare";
        $this->merge(['type' => $this->route('type')]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        echo "rullleeees";
        return [
            'type' => 'required'
        ];
    }

    public function messages()
    {
        echo("messssssagesesesese");
        return [
            'type.required'=>"messages.errors.element_not_found"
        ];
    }
}
