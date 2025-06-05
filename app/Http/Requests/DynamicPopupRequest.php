<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DynamicPopupRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'                => 'required|string|max:100',
            'summary'              => 'required|string|max:191',
            'banner'               => 'required',
            'btn_link'             => 'required|string|max:191',
            'btn_text'             => 'required|string|max:191',
            'btn_text_color'       => 'required|string|max:191',
            'btn_background_color' => 'required|string|max:191'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'                => translate('Popup title is required'),
            'summary.required'              => translate('Popup summary is required'),
            'banner.required'               => translate('Popup image is required'),
            'btn_link.required'              => translate('Link is required.'),
            'btn_text.required'             => translate('Button Text is required'),
            'btn_text_color.required'        => translate('Button Text Color is required.'),
            'btn_background_color.required' => translate('Button Color is required')
        ];
    }
}
