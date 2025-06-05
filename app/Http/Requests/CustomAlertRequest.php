<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

class CustomAlertRequest extends FormRequest
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
        $id = $this->custom_alert ? $this->custom_alert->id : null;
        return [
            'type'             => 'required|string|max:100',
            'description'      => 'required|string|max:200',
            'banner'           => [new RequiredIf($id != 1)],
            'link'             => 'required|string|max:191',
            'text_color'       => 'required|string|max:191',
            'background_color' => 'required|string|max:191'
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
            'type.required'             => translate('Alert Size is required'),
            'description.required'      => translate('Alert Text  is required'),
            'banner.required'           => translate('Alert image is required'),
            'link.required'             => translate('Link is required.'),
            'text_color.required'       => translate('Text Color is required.'),
            'background_color.required' => translate('Background  Color is required')
        ];
    }
}
