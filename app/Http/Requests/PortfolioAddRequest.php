<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PortfolioAddRequest extends FormRequest
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
            "cover" => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'content'=>'required|min:30',
            'title'               => 'required|string|min:20|max:60',
            'subcategory'         => 'required|exists:categories,id',
            'images'         => 'required',
            'images.*'       => 'image|mimes:png,jpg,jpeg|max:2048',
            'url'=>"required|url",
            'completed_date'=>'required|date_format:Y-m-d|before_or_equal:' . $before
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
            'cover.required' => __("messages.validation.cover_required"),
            'content.required'=>__('messages.validation.portfolio_details_required'),
            'content.min'=>__('messages.validation.portfolio_details_min'),
            'title.required'=>__('messages.validation.title_required'),
            'title.min' =>__("messages.validation.title_size"),
            'title.max' =>__("messages.validation.title_size"),
            'title.string' =>__("messages.validation.string"),
            'subcategory.required' =>__("messages.validation.subcategory_required"),
            'subcategory.exists' =>__("messages.validation.exists"),
            "images.required" =>__("messages.product.galaries_required"),
            'images.*.image' => __("messages.validation.images_mimes"),
            'images.*.mimes' => __("messages.validation.images_mimes"),
            'images.*.max' => __("messages.validation.images_size"),
            'completed_date.required' => __("messages.validation.date_of_birth_required"),
        ];
    }
}
