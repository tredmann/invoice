<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\InvoiceTemplates;

use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\UniqueNumber;
use App\Services\Invoices\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class InvoicePdfViewTest extends TestCase
{
    use DatabaseTransactions;
    use MakesTenants;

    public function testDefaultInvoicePdfCompilesAndContainsRequiredFields(): void
    {
        $html = $this->renderInvoiceView('default.invoices.invoice-pdf');

        self::assertStringContainsString('@page', $html, 'expected CSS Paged Media @page rule');
        self::assertStringContainsString('counter(page)', $html, 'expected page numbering via counter(page)');
    }

    public function testDefaultCancellationInvoicePdfCompilesAndContainsRequiredFields(): void
    {
        $html = $this->renderInvoiceView('default.invoices.cancelled-invoice-pdf');

        self::assertStringContainsString('@page', $html);
        self::assertStringContainsString('counter(page)', $html);
    }

    public function testGoerzwerkInvoicePdfCompilesAndContainsRequiredFields(): void
    {
        $html = $this->renderInvoiceView('goerzwerk.pdf.invoice');

        self::assertStringContainsString('@page', $html);
        self::assertStringContainsString('counter(page)', $html);
    }

    public function testGoerzwerkCancellationInvoicePdfCompilesAndContainsRequiredFields(): void
    {
        $html = $this->renderInvoiceView('goerzwerk.pdf.invoice-cancellation');

        self::assertStringContainsString('@page', $html);
        self::assertStringContainsString('counter(page)', $html);
        self::assertStringContainsString('Stornierung', $html);
    }

    private function renderInvoiceView(string $view): string
    {
        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'quantity' => 1.0,
            'price_each' => 1000,
            'currency' => 'EUR',
            'without_tax' => 1000,
            'tax_rate' => 0.19,
            'with_tax' => 1190,
            'unit' => 'h',
            'detail' => 'smoke',
        ]);

        $invoice = $invoice->fresh();

        $html = view($view, [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'legalInfo' => $invoice->customer->tenant->currentLegalInfo,
            'generalInfo' => $invoice->customer->tenant->currentGeneralInfo,
            'totalPerTax' => InvoiceService::totalPerTax($invoice->lineItems),
            'uniqueNumber' => new UniqueNumber(),
        ])->render();

        self::assertStringContainsString((string) $invoice->invoice_no, $html, 'expected invoice number in HTML');
        self::assertStringContainsString((string) $invoice->customer->customer_no, $html, 'expected customer number in HTML');
        self::assertStringContainsString('smoke', $html, 'expected line item detail in HTML');

        return $html;
    }
}
