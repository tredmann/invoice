<?php

namespace App\Http\Requests;

use App\Models\MasterInvoice;
use App\Models\Money;
use App\Rules\CurrencySet;
use Illuminate\Foundation\Http\FormRequest;

class MasterLineItemRequest extends FormRequest
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
        $rules = [
            'quantity' => 'required|numeric|regex:/[\d]+.[\d]{2}/|max:999999999999',
            'price_each' => 'required|numeric|regex:/[\d]+.[\d]{2}/|max:9999999',
            'tax_rate' => ['required', 'in:' . implode(',', Money::DE_TAX_RATES)],
            'unit' => 'nullable|string|max:30',
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
