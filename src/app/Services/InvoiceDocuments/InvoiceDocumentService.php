<?php

namespace App\Services\InvoiceDocuments;

use App\Interfaces\InvoiceInterface;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class InvoiceDocumentService
{
    public function generateStoragePath(InvoiceInterface $invoice): string
    {
        $year = $invoice->getInvoiceDate()->format('Y');
        $month = $invoice->getInvoiceDate()->format('m');

        return sprintf('/%s/%s/%s/%s/%s.pdf', 'inv', App::environment(), $year, $month, $invoice->getInvoiceNumber());
    }

    private function toStorage(array $documentData, Invoice $invoice): string
    {
        $uploadPath = $this->generateStoragePath($invoice);
        $storage = config('settings.upload_storage');

        Storage::disk($storage)->put($uploadPath, $documentData['content']);

        return $uploadPath;
    }

    public function deleteInvoiceDocument(InvoiceDocument $invoiceDocument): void
    {
        $storage = $invoiceDocument->storage;

        $invoiceDocument->delete();

        Storage::disk($storage)->delete($invoiceDocument->path);
    }

    public function saveInvoiceDocument(array $documentData, Invoice $invoice): void
    {
        $storagePath = $this->toStorage($documentData, $invoice);

        $fileData = [
            'path' => $storagePath,
            'user_id' => 1,
            'invoice_id' => $documentData['invoice_id'],
            'mime_type' => $documentData['mimeType'],
            'storage' => config('settings.upload_storage'),
            'type' => InvoiceDocument::TYPE_INVOICE_DOCUMENT,
        ];

        InvoiceDocument::create($fileData);
    }
}
