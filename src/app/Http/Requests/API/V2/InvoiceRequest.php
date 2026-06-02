<?php

namespace App\Http\Requests\API\V2;

use App\Models\Invoice;
use App\Models\Money;
use App\Rules\AllowTenantMembers;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'customer_id' => ['required', 'uuid', 'exists:customers,id', new AllowTenantMembers()],
            'currency' => ['required', 'string', 'in:' . implode(',', Money::CURRENCIES)],
            'status' => ['sometimes', 'required', 'string', 'in:' . implode(',', [Invoice::STATUS_DRAFT, Invoice::STATUS_OPEN])],
            'open_at' => ['nullable', 'date', 'prohibited_unless:status,' . Invoice::STATUS_OPEN, 'required_if:status,' . Invoice::STATUS_OPEN],
            'days_till_due' => ['nullable', 'int', 'in:' . implode(',', Invoice::DAYS_TILL_DUE), 'prohibited_unless:status,' . Invoice::STATUS_OPEN, 'required_if:status,' . Invoice::STATUS_OPEN],
            'performed_when' => ['nullable', 'string', 'max:80', 'required_if:status,' . Invoice::STATUS_OPEN],
            'lines' => [
                'quantity' => ['required', 'numeric', 'regex:/[\d]+.[\d]{2}/', 'max:999999999999'],
                'price_each' => ['required', 'numeric', 'regex:/[\d]+.[\d]{2}/', 'max:9999999'],
                'tax_rate' => ['required', 'float', 'in:' . implode(',', Money::DE_TAX_RATES)],
                'unit' => ['nullable', 'string', 'max:30'],
                'detail' => ['required', 'string', 'max:50'],
                'detail_plus' => ['nullable', 'string', 'max:255'],
            ],
        ];
    }
}
