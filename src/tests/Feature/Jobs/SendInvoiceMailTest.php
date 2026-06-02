<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendInvoiceMail;
use App\Mail\InvoiceMail;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class SendInvoiceMailTest extends TestCase
{
    use DatabaseTransactions;
    use MakesTenants;

    public function testHappyPathSendsOneMailPerReceiverAndMarksMailed(): void
    {
        Mail::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()
            ->for($customer)
            ->open()
            ->withMailStatus(Invoice::MAIL_STATUS_MAILABLE)
            ->create();
        InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create();

        (new SendInvoiceMail($invoice))->handle();

        Mail::assertSent(InvoiceMail::class, function (InvoiceMail $mail) use ($customer) {
            return $mail->hasTo($customer->customerMailReceivers->first()->email);
        });
        Mail::assertSent(InvoiceMail::class, 1);

        self::assertSame(Invoice::MAIL_STATUS_MAILED, $invoice->fresh()->mail_status);
    }

    public function testSendsToAllReceivers(): void
    {
        Mail::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        // makeTenantWithEverything seeds 1 receiver; add 2 more.
        CustomerMailReceiver::factory()->count(2)->create(['customer_id' => $customer->id]);

        $invoice = Invoice::factory()->for($customer)->open()->create();
        InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create();

        (new SendInvoiceMail($invoice->fresh()))->handle();

        Mail::assertSent(InvoiceMail::class, 3);
    }

    public function testPartialFailureContinuesAndMarksMailError(): void
    {
        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        // Seed 3 receivers: 1 from MakesTenants + 2 more.
        CustomerMailReceiver::factory()->count(2)->create(['customer_id' => $customer->id]);

        $invoice = Invoice::factory()->for($customer)->open()->create();
        InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create();

        $sendCalls = 0;
        $pendingMail = \Mockery::mock(\Illuminate\Mail\PendingMail::class);
        $pendingMail->shouldReceive('send')->andReturnUsing(function () use (&$sendCalls) {
            $sendCalls++;
            if ($sendCalls === 2) {
                throw new \RuntimeException('SMTP down for one recipient');
            }
        });

        Mail::shouldReceive('to')->times(3)->andReturn($pendingMail);

        (new SendInvoiceMail($invoice->fresh()))->handle();

        // All 3 sends attempted (loop did not abort on first throw); mail_status
        // reflects that at least one delivery failed.
        self::assertSame(3, $sendCalls, 'expected the loop to attempt all 3 sends');
        self::assertSame(Invoice::MAIL_STATUS_ERROR, $invoice->fresh()->mail_status);
    }
}
