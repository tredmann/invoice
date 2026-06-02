<?php

namespace App\Http\Requests;

use App\Models\Money;
use App\Rules\CurrencySet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LineItemRequest extends FormRequest
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
        if ($this->isMethod(self::METHOD_POST)) {
            return Gate::authorize('stop-post-open-invoice-action', $this->invoice);
        }

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
            'unit' => 'nullable|string|max:30',
            'detail' => 'required|string|max:255',
            'detail_plus' => 'nullable|string|max:255',
        ];

        if ($this->isMethod(self::METHOD_POST)) {
            $rules += [
                'currency' => [
                    'required',
                    'string',
                    'in:' . implode(',', Money::CURRENCIES),
                    new CurrencySet($this->invoice, $this->currency),
                ],
            ];
        }

        return $rules;
    }
}
