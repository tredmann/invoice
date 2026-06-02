<?php

namespace App\Http\Requests;

use App\Models\MasterInvoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MasterInvoiceActiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'days_till_due' => ['required', 'int', 'in:' . implode(',', MasterInvoice::DAYS_TILL_DUE)],
            'billing_frequency' => ['required', 'string', 'in:' . implode(',', MasterInvoice::BILLING_FREQUENCIES)],
            'next_print' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    #[\Override]
    public function messages(): array
    {
        return [
            'next_print.after_or_equal' => 'Das angegebene Datum liegt in der Vergangenheit.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->hasNoLineItems()) {
                $validator->errors()->add('masterLineItems', 'Die Abo-Rechnung hat keine Rechnungspositionen!');
            }
        });
    }

    public function hasNoLineItems(): bool
    {
        $masterInvoice = request()->route('masterInvoice');

        if ($masterInvoice instanceof MasterInvoice) {
            return count($masterInvoice->masterLineItems) === 0;
        }

        return false;
    }
}
