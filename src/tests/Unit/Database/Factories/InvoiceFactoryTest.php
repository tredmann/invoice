<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Tenant::factory(['owner_id' => $user])->create();
        Customer::factory()->create();
    }

    public function test_cancelled_state_sets_status_to_cancelled(): void
    {
        $invoice = Invoice::factory()->open()->cancelled()->create();

        $this->assertSame(Invoice::STATUS_CANCELLED, $invoice->status);
        $this->assertNotNull($invoice->invoice_no, 'Cancelled invoice should retain its invoice_no');
    }

    public function test_cancellation_invoice_state_pairs_with_cancelled_invoice(): void
    {
        $cancelled = Invoice::factory()->open()->cancelled()->create();
        $cancellation = Invoice::factory()->cancellationInvoice($cancelled)->create();

        $this->assertSame(Invoice::STATUS_CANCELLATION_INVOICE, $cancellation->status);
        $this->assertSame($cancelled->id, $cancellation->cancelled_invoice_id);
        $this->assertNotNull($cancellation->invoice_no, 'Cancellation invoice should have its own invoice_no');
    }

    public function test_pdf_error_state_sets_status_to_open_pdf_error(): void
    {
        $invoice = Invoice::factory()->open()->pdfError()->create();

        $this->assertSame(Invoice::STATUS_OPEN_PDF_ERROR, $invoice->status);
        $this->assertNotNull($invoice->invoice_no);
    }

    public function test_with_mail_status_state_overrides_mail_status(): void
    {
        $invoice = Invoice::factory()
            ->open()
            ->withMailStatus(Invoice::MAIL_STATUS_MAILED)
            ->create();

        $this->assertSame(Invoice::MAIL_STATUS_MAILED, $invoice->mail_status);
    }
}
