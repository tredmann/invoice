<?php

namespace Tests\Concerns;

use App\Models\Invoice;
use PHPUnit\Framework\Assert;

trait AssertsMoney
{
    protected function assertCentsEqual(int $expectedCents, $actualCents, string $message = ''): void
    {
        if (is_int($actualCents)) {
            $normalised = $actualCents;
        } elseif (is_string($actualCents) && ctype_digit(ltrim($actualCents, '-'))) {
            $normalised = (int) $actualCents;
        } else {
            Assert::fail(sprintf(
                'Expected integer cents, got %s. %s',
                var_export($actualCents, true),
                $message
            ));
        }

        Assert::assertSame(
            $expectedCents,
            $normalised,
            $message ?: sprintf('Expected %d cents, got %d', $expectedCents, $normalised)
        );
    }

    protected function assertInvoiceTotalsMatchLineItems(Invoice $invoice): void
    {
        Assert::assertNotNull($invoice->total_without_tax, 'Invoice total_without_tax should not be null');
        Assert::assertNotNull($invoice->total_with_tax, 'Invoice total_with_tax should not be null');

        $sumWithoutTax = (int) $invoice->lineItems->sum('without_tax');
        $sumWithTax = (int) $invoice->lineItems->sum('with_tax');

        Assert::assertSame(
            $sumWithoutTax,
            (int) $invoice->total_without_tax,
            'Invoice total_without_tax does not equal sum of line item without_tax'
        );
        Assert::assertSame(
            $sumWithTax,
            (int) $invoice->total_with_tax,
            'Invoice total_with_tax does not equal sum of line item with_tax'
        );
    }
}
