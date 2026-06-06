<?php

declare(strict_types=1);

namespace App\Models;

class Money implements \Stringable
{
    public const CURRENCY_EUR = 'EUR';

    public const CURRENCY_US_DOLLAR = 'USD';

    public const CURRENCIES = [self::CURRENCY_EUR, self::CURRENCY_US_DOLLAR];

    public const DE_TAX_RATES = [0.19, 0.07];

    private readonly float $money;

    public function __construct(float $money, private readonly null|string $currency = null)
    {
        $this->money = $money / 100;
    }

    public function __toString(): string
    {
        return (string) $this->currency !== '' && (string) $this->currency !== '0' ? $this->currencyFormat() : (string) $this->money;
    }

    private function currencyFormat(): string
    {
        switch ($this->currency) {
            case self::CURRENCY_EUR:
                $x = number_format($this->money, 2, ',', '.');

                return $x . ' &euro;';

            case self::CURRENCY_US_DOLLAR:
                $x = number_format($this->money, 2, '.', ',');

                return '$' . $x;
        }
        return '';
    }
}
