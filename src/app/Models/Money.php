<?php

namespace App\Models;

class Money
{
    public const CURRENCY_EUR = 'EUR';

    public const CURRENCY_US_DOLLAR = 'USD';

    public const CURRENCIES = [self::CURRENCY_EUR, self::CURRENCY_US_DOLLAR];

    public const DE_TAX_RATES = [0.19, 0.07];

    private null|string $currency;

    private float $money;

    public function __construct(float $money, ?string $currency = null)
    {
        $this->money = $money / 100;
        $this->currency = $currency;
    }

    public function __toString()
    {
        return $this->currency ? $this->currencyFormat() : (string) $this->money;
    }

    private function currencyFormat()
    {
        switch ($this->currency) {
            case self::CURRENCY_EUR:
                $x = number_format($this->money, 2, ',', '.');
                $x .= ' &euro;';

                return $x;

            case self::CURRENCY_US_DOLLAR:
                $x = number_format($this->money, 2, '.', ',');

                return '$' . $x;
        }
    }
}
