<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDocumentFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testDefaultStateCreatesInvoiceDocument(): void
    {
        $invoice = $this->makeInvoice();

        $doc = InvoiceDocument::factory()->for($invoice)->create();

        self::assertSame(InvoiceDocument::TYPE_INVOICE_DOCUMENT, $doc->type);
        self::assertNotNull($doc->path);
        self::assertSame($invoice->id, $doc->invoice_id);
    }

    public function testAttachmentState(): void
    {
        $invoice = $this->makeInvoice();

        $doc = InvoiceDocument::factory()->attachment()->for($invoice)->create();

        self::assertSame(InvoiceDocument::TYPE_ATTACHMENT, $doc->type);
    }

    public function testInvoiceDocumentStateOverridesAttachment(): void
    {
        $invoice = $this->makeInvoice();

        $doc = InvoiceDocument::factory()
            ->attachment()
            ->invoiceDocument()
            ->for($invoice)
            ->create();

        self::assertSame(InvoiceDocument::TYPE_INVOICE_DOCUMENT, $doc->type);
    }

    public function testForOverrideDoesNotCreateOrphanInvoice(): void
    {
        $invoice = $this->makeInvoice();
        $invoiceCountBefore = \App\Models\Invoice::count();
        $userCountBefore = \App\Models\User::count();

        \App\Models\InvoiceDocument::factory()->for($invoice)->create();

        self::assertSame(
            $invoiceCountBefore,
            \App\Models\Invoice::count(),
            'factory should not create an orphan Invoice when ->for() provides one'
        );
        self::assertSame(
            $userCountBefore,
            \App\Models\User::count(),
            'factory should not create an orphan User when invoice already has one'
        );
    }

    private function makeInvoice(): Invoice
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();

        return Invoice::factory()->for($customer)->create();
    }
}
