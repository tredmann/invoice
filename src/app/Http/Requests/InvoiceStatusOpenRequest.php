<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InvoiceStatusOpenRequest extends FormRequest
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
            'days_till_due' => ['required', 'in:' . implode(',', Invoice::DAYS_TILL_DUE), 'int'],
            'performed_when' => 'required|string|max:40',
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
                $validator->errors()->add('lineItems', 'Die Rechnung hat keine Rechnungspositionen!');
            }
        });
    }

    public function hasNoLineItems()
    {
        $invoice = $this->route('invoice');
        if (! ($invoice instanceof Invoice)) {
            return true;
        }

        return count($invoice->lineItems) === 0;
    }
}
