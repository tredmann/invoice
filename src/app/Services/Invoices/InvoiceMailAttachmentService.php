<?php

namespace App\Services\Invoices;

use App\Interfaces\InvoiceInterface;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class InvoiceMailAttachmentService
{
    public function generateAttachmentPath(InvoiceInterface $invoice, string $attachment): string
    {
        $year = $invoice->getInvoiceDate()->format('Y');
        $month = $invoice->getInvoiceDate()->format('m');

        return sprintf('/%s/%s/%s/%s/%s-%s', 'inv', App::environment(), $year, $month, $invoice->getInvoiceNumber(), $attachment);
    }

    private function toStorage(UploadedFile $attachment, Invoice $invoice): string
    {
        $uploadPath = $this->generateAttachmentPath($invoice, $attachment->getClientOriginalName());
        $storage = config('settings.upload_storage');

        Storage::disk($storage)->put($uploadPath, $attachment->getContent());

        return $uploadPath;
    }

    public function deleteInvoiceDocument(InvoiceDocument $invoiceDocument): void
    {
        $storage = $invoiceDocument->storage;

        $invoiceDocument->delete();

        Storage::disk($storage)->delete($invoiceDocument->path);
    }

    public function saveInvoiceDocument(UploadedFile $attachment, Invoice $invoice): void
    {
        $previousAttachment = $invoice->getAttachment();
        if ($previousAttachment instanceof \App\Models\InvoiceDocument) {
            $this->deleteInvoiceDocument($previousAttachment);
        }

        $storagePath = $this->toStorage($attachment, $invoice);

        $fileData = [
            'path' => $storagePath,
            'user_id' => 1,
            'invoice_id' => $invoice->id,
            'mime_type' => $attachment->getMimeType(),
            'storage' => config('settings.upload_storage'),
            'type' => InvoiceDocument::TYPE_ATTACHMENT,
        ];

        InvoiceDocument::create($fileData);
    }
}
