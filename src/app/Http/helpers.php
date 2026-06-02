<?php

declare(strict_types=1);

if (! function_exists('money')) {
    function money(int $money, ?string $currency = null): ?\App\Models\Money
    {
        return $money !== 0 ?
            new App\Models\Money($money, $currency) : null;
    }
}
