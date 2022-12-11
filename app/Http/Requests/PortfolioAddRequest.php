<?php

namespace App\Http\Requests;

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
        return [
            'portfolio_url' => 'required|url',
            "cover" => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'content'=>'required|min:30',
            'title'               => 'required|string|min:20|max:60',
            'images'         => 'required',
            'images.*'       => 'image|mimes:png,jpg,jpeg|max:2048',
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
            'portfolio.required' => __("messages.validation.portfolio_required"),
            'portfolio.url'=>__("messages.validation.url"),
            'cover.required' => __("messages.validation.cover_required"),
            'content.required'=>__('messages.validation.content_required'),
            'content.min'=>__('messages.validation.content_min'),
            'title.required'=>__('messages.validation.title_required'),
            'title.min' =>__("messages.validation.title_size"),
            'title.max' =>__("messages.validation.title_size"),
            'title.string' =>__("messages.validation.string"),
            "images.required" =>__("messages.product.galaries_required"),
            'images.*.image' => __("messages.validation.images_mimes"),
            'images.*.mimes' => __("messages.validation.images_mimes"),
            'images.*.max' => __("messages.validation.images_size"),
        ];
    }
}
