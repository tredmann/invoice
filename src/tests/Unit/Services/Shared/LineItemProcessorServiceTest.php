<?php

namespace Tests\Unit\Services\Shared;

use App\Services\Shared\LineItemProcessorService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LineItemProcessorServiceTest extends TestCase
{
    use WithFaker;

    public function testProcessLineItemData()
    {
        $input = [
            'quantity' => '1.05',
            'price_each' => '10.55',
            'tax_rate' => 0.07,
            'currency' => 'EUR',
        ];

        $expected_output = [
            'quantity' => 1.05,
            'price_each' => 1055,
            'without_tax' => 1108,
            'with_tax' => 1186,
            'tax_rate' => 0.07,
            'currency' => 'EUR',
        ];

        self::assertEquals($expected_output, LineItemProcessorService::processLineItemData($input));
    }

    public function testCalcWithoutTax()
    {
        self::assertEquals(177700, LineItemProcessorService::calcWithoutTax(10000, 17.77));
    }

    public function testCalcWithTax()
    {
        self::assertEquals(10700, LineItemProcessorService::calcWithTax(10000, 0.07));
    }

    public function testCalcWithoutTaxZeroQuantity(): void
    {
        self::assertSame(0, LineItemProcessorService::calcWithoutTax(1999, 0.0));
    }

    public function testCalcWithoutTaxZeroPrice(): void
    {
        self::assertSame(0, LineItemProcessorService::calcWithoutTax(0, 5.0));
    }

    public function testCalcWithoutTaxNegativeQuantity(): void
    {
        // pinning current behavior; negative quantity yields negative cents
        self::assertSame(-1999, LineItemProcessorService::calcWithoutTax(1999, -1.0));
    }

    public function testCalcWithoutTaxRoundsHalfAwayFromZero(): void
    {
        // priceEach=1, quantity=2.5  =>  2.5 cents, rounded => 3
        self::assertSame(3, LineItemProcessorService::calcWithoutTax(1, 2.5));
    }

    public function testCalcWithoutTaxLargeQuantity(): void
    {
        // 1_000_000 * 99.99 — float precision must not produce drift > 1 cent
        $result = LineItemProcessorService::calcWithoutTax(1_000_000, 99.99);
        self::assertEqualsWithDelta(99_990_000, $result, 1);
    }

    public function testCalcWithTaxZeroRate(): void
    {
        self::assertSame(1000, LineItemProcessorService::calcWithTax(1000, 0.0));
    }

    public function testCalcWithTaxStandardGermanRate(): void
    {
        // 1000 cents * 0.19 = 190 tax -> 1190
        self::assertSame(1190, LineItemProcessorService::calcWithTax(1000, 0.19));
    }

    public function testCalcWithTaxReducedGermanRate(): void
    {
        // 1000 cents * 0.07 = 70 tax -> 1070
        self::assertSame(1070, LineItemProcessorService::calcWithTax(1000, 0.07));
    }

    public function testCalcWithTaxFractionalCentBoundary(): void
    {
        // 333 * 0.07 = 23.31 -> withTax 333 + 23 = 356 (rounded from 356.31)
        self::assertSame(356, LineItemProcessorService::calcWithTax(333, 0.07));
    }

    public function testCalcWithTaxRoundsHalfAwayFromZero(): void
    {
        // 50 * 0.07 = 3.50 -> withTax 50 + 4 = 54 (PHP round() defaults to half-away-from-zero)
        self::assertSame(54, LineItemProcessorService::calcWithTax(50, 0.07));
    }

    public function testProcessLineItemDataTruncatesPrice(): void
    {
        // pinning current behavior; (int)(19.999 * 100) = 1999 due to truncation
        $result = LineItemProcessorService::processLineItemData([
            'price_each' => 19.999,
            'quantity' => 1,
            'tax_rate' => 0.19,
        ]);

        self::assertSame(1999, $result['price_each']);
    }

    public function testProcessLineItemDataConvertsEurosToCents(): void
    {
        // pinning current behavior: (int)(19.99 * 100) = 1998 due to float truncation
        // (IEEE 754: 19.99 * 100 = 1998.9999…, truncated to 1998)
        $result = LineItemProcessorService::processLineItemData([
            'price_each' => 19.99,
            'quantity' => 2,
            'tax_rate' => 0.19,
        ]);

        self::assertSame(1998, $result['price_each']);
        self::assertSame(3996, $result['without_tax']);
        self::assertSame(4755, $result['with_tax']);
    }
}
