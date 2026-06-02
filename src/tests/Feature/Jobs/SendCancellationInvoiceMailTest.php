<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendCancellationInvoiceMail;
use App\Mail\CancellationInvoiceMail;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class SendCancellationInvoiceMailTest extends TestCase
{
    use DatabaseTransactions;
    use MakesTenants;

    public function testHappyPathSendsCancellationMailAndMarksMailed(): void
    {
        Mail::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $original = Invoice::factory()->for($customer)->cancelled()->create();
        $cancellation = Invoice::factory()->for($customer)->cancellationInvoice($original)->create();
        InvoiceDocument::factory()->for($cancellation)->invoiceDocument()->create();

        (new SendCancellationInvoiceMail($cancellation->fresh()))->handle();

        Mail::assertSent(CancellationInvoiceMail::class, 1);
        self::assertSame(Invoice::MAIL_STATUS_MAILED, $cancellation->fresh()->mail_status);
    }

    public function testCancellationMailIncludesBcc(): void
    {
        Mail::fake();
        config(['mail.bcc.address' => 'bcc@example.test', 'mail.bcc.name' => 'BCC Recipient']);

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $original = Invoice::factory()->for($customer)->cancelled()->create();
        $cancellation = Invoice::factory()->for($customer)->cancellationInvoice($original)->create();
        InvoiceDocument::factory()->for($cancellation)->invoiceDocument()->create();

        (new SendCancellationInvoiceMail($cancellation->fresh()))->handle();

        // Mail::fake() captures the Mailable but does NOT call build(). Render
        // it through the container to populate the bcc/from/subject from build().
        Mail::assertSent(CancellationInvoiceMail::class, function (CancellationInvoiceMail $mail) {
            app()->call([$mail, 'build']);

            return $mail->hasBcc('bcc@example.test');
        });
    }

    public function testPartialFailureContinuesAndMarksMailError(): void
    {
        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        CustomerMailReceiver::factory()->count(2)->create(['customer_id' => $customer->id]);

        $original = Invoice::factory()->for($customer)->cancelled()->create();
        $cancellation = Invoice::factory()->for($customer)->cancellationInvoice($original)->create();
        InvoiceDocument::factory()->for($cancellation)->invoiceDocument()->create();

        $sendCalls = 0;
        $pendingMail = \Mockery::mock(\Illuminate\Mail\PendingMail::class);
        $pendingMail->shouldReceive('send')->andReturnUsing(function () use (&$sendCalls) {
            $sendCalls++;
            if ($sendCalls === 2) {
                throw new \RuntimeException('SMTP down for one recipient');
            }
        });

        Mail::shouldReceive('to')->times(3)->andReturn($pendingMail);

        (new SendCancellationInvoiceMail($cancellation->fresh()))->handle();

        self::assertSame(3, $sendCalls, 'expected the loop to attempt all 3 sends');
        self::assertSame(Invoice::MAIL_STATUS_ERROR, $cancellation->fresh()->mail_status);
    }
}
