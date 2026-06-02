<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegalInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
            'registry_court' => 'required|string',
            'registry_no' => 'required|string',
            'company_owner' => 'required|string',
            'tax_no' => 'required|string',
            'vat_no' => 'required|string',
            'swift_bic' => 'required|string',
            'iban' => 'required|string',
            'bank_institute' => 'required|string',
        ];
    }
}
