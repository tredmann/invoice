<?php

namespace App\Http\Requests;

use App\Enums\UnitCode;
use App\Models\MasterInvoice;
use App\Models\Money;
use App\Rules\CurrencySet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MasterLineItemRequest extends FormRequest
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
        $rules = [
            'quantity' => 'required|numeric|regex:/[\d]+.[\d]{2}/|max:999999999999',
            'price_each' => 'required|numeric|regex:/[\d]+.[\d]{2}/|max:9999999',
            'tax_rate' => ['required', 'in:' . implode(',', Money::DE_TAX_RATES)],
            'unit' => ['required', Rule::enum(UnitCode::class)],
            'detail' => 'required|string|max:50',
            'detail_plus' => 'nullable|string|max:255',
        ];

        if ($this->isMethod(self::METHOD_POST)) {
            $rules += [
                'master_invoice_id' => ['required', 'string', 'exists:master_invoices,id'],
                'currency' => ['required', 'string', new CurrencySet($this->getMasterInvoice(), $this->currency)],
            ];
        }

        return $rules;
    }

    public function getMasterInvoice()
    {
        return MasterInvoice::findOrFail($this->master_invoice_id);
    }
}
