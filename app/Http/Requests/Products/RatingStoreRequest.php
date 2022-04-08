<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class RatingStoreRequest extends FormRequest
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
            'rating' => 'required|integer|between:1,10',
            'comment' => 'required',
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
            'rating.required' =>__("messages.validation.rating_required"),
            'rating.integer' => __("messages.validation.numeric"),
            'rating.between' => __("messages.validation.rating_between"),
            'comment.required' => __("messages.validation.comment_required"),
            
        ];
    }
}
