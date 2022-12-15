<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PortfolioUpdateRequest extends FormRequest
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
        $before = $dt1->now()->format('Y-m-d');
        return [
            "cover" => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
            'content'=>'sometimes|min:30',
            'title'               => 'sometimes|string|min:20|max:60',
            'images.*'       => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
            'url'=>"sometimes|url",
            'completed_date'=>'sometimes|date_format:Y-m-d|before_or_equal:' . $before
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
            'url'=>__("messages.validation.url"),
            'content.min'=>__('messages.validation.portfolio_details_min'),
            'title.min' =>__("messages.validation.title_size"),
            'title.max' =>__("messages.validation.title_size"),
            'title.string' =>__("messages.validation.string"),
            'images.*.image' => __("messages.validation.images_mimes"),
            'images.*.mimes' => __("messages.validation.images_mimes"),
            'images.*.max' => __("messages.validation.images_size"),
        ];
    }
}
