<?php

namespace Tests\Feature\Concerns;

use App\Jobs\GeneratePDF;
use App\Mail\InvoiceMail;
use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\FakesInvoicePipeline;
use Tests\TestCase;

class FakesInvoicePipelineTest extends TestCase
{
    use RefreshDatabase;
    use FakesInvoicePipeline;

    public function testFakesAllThreeFacades(): void
    {
        $this->fakeInvoicePipeline();

        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();

        GeneratePDF::dispatch($invoice);

        Queue::assertPushed(GeneratePDF::class);
        Mail::assertNothingSent();
        $this->assertEmpty(Storage::disk('local')->allFiles('/'));
    }

    public function testAssertPdfStoredForPassesWhenDocumentExists(): void
    {
        $this->fakeInvoicePipeline();

        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();

        $path = 'invoices/'.$invoice->id.'.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 fake');

        InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create([
            'path'    => $path,
            'storage' => 'local',
        ]);

        $this->assertPdfStoredFor($invoice);
    }

    public function testAssertInvoiceMailedToPassesWhenSent(): void
    {
        $this->fakeInvoicePipeline();

        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();
        /** @var CustomerMailReceiver $receiver */
        $receiver = CustomerMailReceiver::factory(['customer_id' => $customer->id])->create();
        $invoiceDoc = InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create();

        $recipientEmail = 'recipient@example.test';

        Mail::to($recipientEmail)->send(
            new InvoiceMail($receiver, $invoice, $invoiceDoc, null)
        );

        $this->assertInvoiceMailedTo($invoice, $recipientEmail);
    }
}
