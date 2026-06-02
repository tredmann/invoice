<?php

namespace Tests\Feature\Services\Invoices;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\AssertsMoney;
use Tests\TestCase;

class InvoiceTotalsTest extends TestCase
{
    use DatabaseTransactions;
    use AssertsMoney;

    private function makeInvoice(): Invoice
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();

        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();
        return $invoice;
    }

    private function addLineItem(Invoice $invoice, float $quantity, int $priceCents, float $taxRate): LineItem
    {
        return LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'quantity' => $quantity,
            'price_each' => $priceCents,
            'currency' => 'EUR',
            'without_tax' => (int) round($priceCents * $quantity),
            'tax_rate' => $taxRate,
            'with_tax' => (int) round(($priceCents * $quantity) * (1 + $taxRate)),
            'unit' => 'h',
            'detail' => 'work',
        ]);
    }

    public function testTotalsMatchLineItemsAtStandardRate(): void
    {
        $invoice = $this->makeInvoice();
        $this->addLineItem($invoice, 2.0, 1999, 0.19);
        $this->addLineItem($invoice, 5.0, 500, 0.19);

        $this->assertInvoiceTotalsMatchLineItems($invoice->fresh('lineItems'));
    }

    public function testTotalsMatchLineItemsAtReducedRate(): void
    {
        $invoice = $this->makeInvoice();
        $this->addLineItem($invoice, 3.0, 1000, 0.07);

        $this->assertInvoiceTotalsMatchLineItems($invoice->fresh('lineItems'));
    }

    public function testTotalsMatchLineItemsAtZeroRate(): void
    {
        $invoice = $this->makeInvoice();
        $this->addLineItem($invoice, 1.0, 5000, 0.0);

        $this->assertInvoiceTotalsMatchLineItems($invoice->fresh('lineItems'));
    }

    public function testTotalsMatchLineItemsAtMixedRates(): void
    {
        $invoice = $this->makeInvoice();
        $this->addLineItem($invoice, 1.0, 1000, 0.19);
        $this->addLineItem($invoice, 2.0, 500, 0.07);
        $this->addLineItem($invoice, 1.0, 100, 0.0);

        $this->assertInvoiceTotalsMatchLineItems($invoice->fresh('lineItems'));
    }

    public function testGetTaxDistributionBreaksDownPerRate(): void
    {
        $invoice = $this->makeInvoice();
        $this->addLineItem($invoice, 1.0, 1000, 0.19); // tax = 190
        $this->addLineItem($invoice, 2.0, 500, 0.07);  // tax = 70

        $dist = $invoice->fresh('lineItems')->getTaxDistribution();

        self::assertArrayHasKey(19, $dist);
        self::assertArrayHasKey(7, $dist);
        self::assertSame(1000, $dist[19]['without_tax']);
        self::assertSame(190, $dist[19]['tax']);
        self::assertSame(1000, $dist[7]['without_tax']);
        self::assertSame(70, $dist[7]['tax']);
    }

    public function testCreatingLineItemUpdatesInvoiceTotals(): void
    {
        $invoice = $this->makeInvoice();
        self::assertSame(0, (int) $invoice->total_with_tax);

        $this->addLineItem($invoice, 1.0, 1000, 0.19);

        $this->assertCentsEqual(1190, $invoice->fresh()->total_with_tax);
    }

    public function testUpdatingLineItemUpdatesInvoiceTotals(): void
    {
        $invoice = $this->makeInvoice();
        $item = $this->addLineItem($invoice, 1.0, 1000, 0.19);

        $item->update([
            'quantity' => 2.0,
            'without_tax' => 2000,
            'with_tax' => 2380,
        ]);

        $this->assertCentsEqual(2380, $invoice->fresh()->total_with_tax);
    }

    public function testDeletingLineItemUpdatesInvoiceTotals(): void
    {
        $invoice = $this->makeInvoice();
        $item = $this->addLineItem($invoice, 1.0, 1000, 0.19);
        $this->addLineItem($invoice, 1.0, 500, 0.19);

        $item->delete();

        $this->assertCentsEqual(595, $invoice->fresh()->total_with_tax);
    }

    public function testFirstLineItemSetsCurrency(): void
    {
        $invoice = $this->makeInvoice();
        // factory may set currency randomly; force null
        $invoice->update(['currency' => null]);

        $this->addLineItem($invoice, 1.0, 1000, 0.19); // factory uses EUR

        self::assertSame('EUR', $invoice->fresh()->currency);
    }
}
