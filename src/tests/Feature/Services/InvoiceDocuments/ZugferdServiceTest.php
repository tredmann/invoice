<?php

declare(strict_types=1);

namespace Tests\Feature\Services\InvoiceDocuments;

use App\Enums\UnitCode;
use App\Exceptions\ZugferdGenerationException;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Services\InvoiceDocuments\ZugferdService;
use horstoeko\zugferd\ZugferdDocumentPdfReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class ZugferdServiceTest extends TestCase
{
    use RefreshDatabase;
    use MakesTenants;

    private const SAMPLE_PDF_PATH = __DIR__.'/../../../../vendor/horstoeko/zugferd/examples/assets/00_ZugferdDocumentPdfBuilder_PrintLayout.pdf';

    public function testEmbedReturnsPdfBytesContainingZugferdMarker(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $pdfBytes = $this->loadSamplePdfBytes();

        $service = new ZugferdService();
        $result = $service->embed($invoice, $pdfBytes);

        self::assertNotSame('', $result);
        self::assertGreaterThan(0, strlen($result));

        $haystack = strtolower($result);
        $hasMarker = str_contains($haystack, 'factur-x')
            || str_contains($haystack, 'zugferd')
            || str_contains($haystack, 'urn:factur-x');

        self::assertTrue(
            $hasMarker,
            'Expected resulting PDF bytes to contain a ZUGFeRD / Factur-X marker.'
        );
    }

    public function testEmbedThrowsWhenVatNumberIsMissing(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->customer->tenant->currentLegalInfo->vat_no = null;
        $invoice->customer->tenant->currentLegalInfo->save();

        $service = new ZugferdService();

        $this->expectException(ZugferdGenerationException::class);
        $service->embed($invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']), $this->loadSamplePdfBytes());
    }

    public function testEmbedThrowsWhenBuyerHasNoName(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->customer->company = '';
        $invoice->customer->name = null;
        $invoice->customer->save();

        $service = new ZugferdService();

        $this->expectException(ZugferdGenerationException::class);
        $this->expectExceptionMessageMatches('/Customer has neither a company name nor a personal name/');
        $service->embed($invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']), $this->loadSamplePdfBytes());
    }

    public function testCancellationInvoiceProducesCreditNoteTypeCode(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->status = Invoice::STATUS_CANCELLATION_INVOICE;
        $invoice->save();

        $service = new ZugferdService();
        $result = $service->embed($invoice, $this->loadSamplePdfBytes());

        // The CreditNote type code 381 should appear in the embedded XML's TypeCode element.
        self::assertStringContainsString('381', $result);
    }

    public function testEmbedThrowsForNonEurCurrency(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->currency = 'USD';
        $invoice->save();

        $service = new ZugferdService();

        $this->expectException(ZugferdGenerationException::class);
        $this->expectExceptionMessageMatches('/ZUGFeRD documents require EUR currency/');
        $service->embed($invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']), $this->loadSamplePdfBytes());
    }

    public function testEmbedThrowsForUnknownCompanyCountry(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->customer->tenant->currentGeneralInfo->country = 'Österreich';
        $invoice->customer->tenant->currentGeneralInfo->save();

        $service = new ZugferdService();

        $this->expectException(ZugferdGenerationException::class);
        $this->expectExceptionMessageMatches('/Company Profile has an unrecognised country/');
        $service->embed($invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']), $this->loadSamplePdfBytes());
    }

    public function testEmbedUsesReducedRateCategoryForSevenPercentVat(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->lineItems()->update(['tax_rate' => 0.07]);
        $invoice = $invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']);

        $service = new ZugferdService();
        $pdf = file_get_contents(base_path('tests/Fixtures/plain.pdf'));
        self::assertNotFalse($pdf, 'Fixture tests/Fixtures/plain.pdf not found');
        $result = $service->embed($invoice, $pdf);
        $xml = ZugferdDocumentPdfReader::getXmlFromContent($result);

        // EN 16931 reduced rate category 'AA' must appear
        self::assertStringContainsString('AA', $xml);
        self::assertStringNotContainsString('<ram:CategoryCode>S</ram:CategoryCode>', $xml);
    }

    public function testCancellationInvoiceDuePayableAmountIsZero(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->update(['status' => Invoice::STATUS_CANCELLATION_INVOICE]);
        $invoice = $invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']);

        $service = new ZugferdService();
        $pdf = file_get_contents(base_path('tests/Fixtures/plain.pdf'));
        self::assertNotFalse($pdf, 'Fixture tests/Fixtures/plain.pdf not found');
        $result = $service->embed($invoice, $pdf);

        $xml = \horstoeko\zugferd\ZugferdDocumentPdfReader::getXmlFromContent($result);
        // DuePayableAmount in ZUGFeRD XML should be 0.00 for a credit note
        self::assertMatchesRegularExpression(
            '/<ram:DuePayableAmount>0\.00<\/ram:DuePayableAmount>/',
            $xml
        );
    }

    public function testEmbedUsesZeroRateCategoryForZeroPercentVat(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->lineItems()->update(['tax_rate' => 0.00, 'with_tax' => \Illuminate\Support\Facades\DB::raw('without_tax')]);
        $invoice = $invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']);

        $service = new ZugferdService();
        $pdf = file_get_contents(base_path('tests/Fixtures/plain.pdf'));
        self::assertNotFalse($pdf, 'Fixture tests/Fixtures/plain.pdf not found');
        $result = $service->embed($invoice, $pdf);
        $xml = ZugferdDocumentPdfReader::getXmlFromContent($result);

        // No STAN_RATE for zero-rate items
        self::assertStringNotContainsString('<ram:CategoryCode>S</ram:CategoryCode>', $xml);
    }

    public function testEmbedThrowsWhenDueDateIsNull(): void
    {
        $invoice = $this->makeFullyConfiguredInvoice();
        $invoice->update(['date_due' => null]);
        $invoice = $invoice->fresh(['customer.tenant.currentLegalInfo', 'customer.tenant.currentGeneralInfo', 'lineItems']);

        $this->expectException(ZugferdGenerationException::class);
        $this->expectExceptionMessageMatches('/due date/i');

        $pdf = file_get_contents(base_path('tests/Fixtures/plain.pdf'));
        self::assertNotFalse($pdf, 'Fixture tests/Fixtures/plain.pdf not found');
        (new ZugferdService())->embed($invoice, $pdf);
    }

    private function makeFullyConfiguredInvoice(): Invoice
    {
        $tenant = $this->makeTenantWithEverything();

        /** @var Customer $customer */
        $customer = $tenant->customers->first();

        /** @var Invoice $invoice */
        $invoice = Invoice::factory()
            ->open()
            ->for($customer)
            ->state([
                'user_id' => $tenant->owner_id,
                'currency' => 'EUR',
            ])
            ->create();

        LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'quantity' => 2.0,
            'price_each' => 1000, // 10.00
            'currency' => 'EUR',
            'without_tax' => 2000, // 20.00
            'tax_rate' => 0.19,
            'with_tax' => 2380, // 23.80
            'unit' => UnitCode::Hour->value,
            'detail' => 'Beratung',
        ]);

        LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'quantity' => 1.0,
            'price_each' => 500, // 5.00
            'currency' => 'EUR',
            'without_tax' => 500, // 5.00
            'tax_rate' => 0.07,
            'with_tax' => 535, // 5.35
            'unit' => UnitCode::Piece->value,
            'detail' => 'Material',
        ]);

        return $invoice->fresh([
            'customer.tenant.currentLegalInfo',
            'customer.tenant.currentGeneralInfo',
            'lineItems',
        ]);
    }

    private function loadSamplePdfBytes(): string
    {
        $bytes = file_get_contents(self::SAMPLE_PDF_PATH);
        self::assertNotFalse($bytes, 'Failed to load sample PDF fixture at '.self::SAMPLE_PDF_PATH);

        return $bytes;
    }
}
