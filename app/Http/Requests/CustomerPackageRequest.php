<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerPackageRequest extends FormRequest
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
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'product_upload' => 'required|numeric',
            'logo'           => 'required'
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
            'name.required'           => translate('Package Name is required'),
            'amount.required'         => translate('Amount is required'),
            'amount.numeric'          => translate('Amount must be a number.'),
            'product_upload.required' => translate('Product Upload is required'),
            'product_upload.numeric'  => translate('Product Upload must be a number.'),
            'logo.required'           => translate('A logo is required')
        ];
    }
}
