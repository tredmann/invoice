<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegalInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
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
