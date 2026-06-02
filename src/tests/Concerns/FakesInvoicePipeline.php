<?php

namespace Tests\Concerns;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Assert;

trait FakesInvoicePipeline
{
    protected function fakeInvoicePipeline(): void
    {
        Mail::fake();
        Queue::fake();
        Storage::fake('local');
    }

    protected function assertPdfStoredFor(Invoice $invoice): void
    {
        $invoice->refresh();
        $document = $invoice->documents
            ->where('type', InvoiceDocument::TYPE_INVOICE_DOCUMENT)
            ->first();

        Assert::assertNotNull($document, sprintf('Invoice %s has no stored PDF document', $invoice->id));
        Storage::disk($document->storage ?? 'local')->assertExists($document->path);
    }

    protected function assertInvoiceMailedTo(Invoice $invoice, string $email): void
    {
        Mail::assertSent(InvoiceMail::class, function (InvoiceMail $mail) use ($email) {
            return $mail->hasTo($email);
        });
    }
}
