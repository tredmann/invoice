<?php

namespace App\Services\Shared;

class LineItemProcessorService
{
    public static function processLineItemData(array $attributes): array
    {
        return array_merge($attributes, [
            'quantity' => $quantity = (float) $attributes['quantity'],
            'price_each' => $priceEach = (int) ($attributes['price_each'] * 100), // the request from the api is always float
            'without_tax' => $withoutTax = self::calcWithoutTax($priceEach, $quantity),
            'with_tax' => $withTax = self::calcWithTax($withoutTax, $attributes['tax_rate']),
        ]);
    }

    public static function calcWithoutTax(int $priceEach, float $quantity): int
    {
        return (int) round($priceEach * $quantity);
    }

    public static function calcWithTax(int $withoutTax, float $taxRate): int
    {
        return (int) round($withoutTax + ($withoutTax * $taxRate));
    }
}
