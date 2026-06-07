<?php

namespace Tests\Feature\Jobs;

use App\Enums\UnitCode;
use App\Jobs\GeneratePDF;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\LineItem;
use App\Modules\InvoiceTemplates\Models\Templates\Template;
use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Services\InvoiceDocuments\InvoiceDocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class GeneratePDFTest extends TestCase
{
    use DatabaseTransactions;
    use MakesTenants;

    public function testFailedHookMarksInvoiceAsOpenPdfError(): void
    {
        Storage::fake('local');

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        (new GeneratePDF($invoice))->failed(new \RuntimeException('boom'));

        self::assertSame(Invoice::STATUS_OPEN_PDF_ERROR, $invoice->fresh()->status);
    }

    public function testMissingTenantTemplateFallsBackToDefault(): void
    {
        Storage::fake('local');
        Pdf::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        // Do NOT register a tenant-specific template. The default 'invoice.pdf'
        // template is registered for TemplateManager::DEFAULT_TENANT in
        // TemplateServiceProvider, so the job should fall back to it.
        (new GeneratePDF($invoice))->handle(
            app(InvoiceDocumentService::class),
            app(TemplateManager::class),
        );

        Pdf::assertViewIs('default.invoices.invoice-pdf');

        $invoice->refresh();
        self::assertSame(Invoice::MAIL_STATUS_MAILABLE, $invoice->mail_status);
        self::assertSame(1, $invoice->documents()->count());
    }

    public function testHappyPathPersistsDocumentAndMarksMailable(): void
    {
        Storage::fake('local');

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        $this->stubTemplate('invoice.pdf', $tenant->getTenantKey(), '%PDF-1.4 fake');

        (new GeneratePDF($invoice))->handle(
            app(InvoiceDocumentService::class),
            app(TemplateManager::class),
        );

        $invoice->refresh();
        self::assertSame(Invoice::MAIL_STATUS_MAILABLE, $invoice->mail_status);
        self::assertSame(
            1,
            $invoice->documents()->where('type', InvoiceDocument::TYPE_INVOICE_DOCUMENT)->count()
        );

        $doc = $invoice->documents->first();
        Storage::disk('local')->assertExists($doc->path);
        self::assertSame('%PDF-1.4 fake', Storage::disk('local')->get($doc->path));
    }

    public function testRealTemplateRenderingProducesPdfBytes(): void
    {
        if (! $this->weasyprintIsAvailable()) {
            self::markTestSkipped('weasyprint binary not on PATH — skipping real-binary smoke test');
        }

        Storage::fake('local');

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        // Add a line item so the template has data to render.
        LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'quantity' => 1.0,
            'price_each' => 1000,
            'currency' => 'EUR',
            'without_tax' => 1000,
            'tax_rate' => 0.19,
            'with_tax' => 1190,
            'unit' => UnitCode::Hour->value,
            'detail' => 'smoke',
        ]);

        // Use the real registered default template — do not stub.
        (new GeneratePDF($invoice->fresh()))->handle(
            app(InvoiceDocumentService::class),
            app(TemplateManager::class),
        );

        $doc = $invoice->fresh()->documents->first();
        self::assertNotNull($doc, 'expected an invoice document to be created');

        $content = Storage::disk('local')->get($doc->path);
        self::assertStringStartsWith('%PDF-', $content, 'expected real PDF bytes');
        self::assertGreaterThan(1000, strlen($content), 'expected a non-trivial PDF');
    }

    private function weasyprintIsAvailable(): bool
    {
        $output = [];
        $status = 1;
        @exec('command -v weasyprint 2>/dev/null', $output, $status);

        return $status === 0 && $output !== [];
    }

    public function testRenderingGoesThroughSpatiePdfFacade(): void
    {
        Storage::fake('local');
        Pdf::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->open()->create();

        (new GeneratePDF($invoice))->handle(
            app(InvoiceDocumentService::class),
            app(TemplateManager::class),
        );

        Pdf::assertViewIs('default.invoices.invoice-pdf');
        Pdf::assertViewHas('invoice');
    }

    private function stubTemplate(string $key, string $tenantKey, string $content): Template
    {
        $template = new class ($tenantKey, $content) implements Template {
            public function __construct(private string $tenantKey, private string $content)
            {
            }

            public function getTenant(): string
            {
                return $this->tenantKey;
            }

            public function getView(): string
            {
                return 'stub';
            }

            public function render(array $data): string
            {
                return $this->content;
            }
        };

        app(TemplateManager::class)->register($key, $template);

        return $template;
    }
}
