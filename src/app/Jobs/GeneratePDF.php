<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Services\InvoiceDocuments\InvoiceDocumentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;

class GeneratePDF implements ShouldQueue
{
    use \Illuminate\Foundation\Queue\Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Invoice $invoice)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(
        InvoiceDocumentService $invoiceDocumentService,
        TemplateManager $templateManager
    ): void {
        $tenantKey = $this->invoice->customer->tenant->getTenantKey();

        if ($this->invoice->status !== Invoice::STATUS_CANCELLATION_INVOICE) {
            $template = $templateManager->getTemplate(templateKey: 'invoice.pdf', tenantKey: $tenantKey);
        } else {
            $template = $templateManager->getTemplate(templateKey: 'invoice-cancellation.pdf', tenantKey: $tenantKey);
        }

        $pdf = $template->render(['invoice' => $this->invoice]);

        // file type-specific info/content
        $pdfData = [
            'user_id' => $this->invoice->user->id,
            'invoice_id' => $this->invoice->id,
            'content' => $pdf,
            'extension' => '.pdf',
            'mimeType' => 'application/pdf',
        ];

        $invoiceDocumentService->saveInvoiceDocument($pdfData, $this->invoice);

        $this->invoice->update([
            'mail_status' => Invoice::MAIL_STATUS_MAILABLE,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->invoice->update([
            'status' => Invoice::STATUS_OPEN_PDF_ERROR,
        ]);
    }
}
