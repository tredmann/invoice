<?php

if (! function_exists('money')) {
    function money(int $money, ?string $currency = null)
    {
        return $money ?
            new App\Models\Money($money, $currency) : null;
    }
}
