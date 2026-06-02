<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Invoice;
use App\Models\MasterInvoice;
use Illuminate\Contracts\Validation\Rule;

class CurrencySet implements Rule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(
        /**
         * @var Invoice | MasterInvoice
         */
        private readonly object $invoice,
        private readonly string $currency
    ) {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->invoice->currency === null || $this->invoice->currency === $this->currency;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This invoice has a currency of ' . $this->invoice->currency . 'not ' . $this->currency;
    }
}
