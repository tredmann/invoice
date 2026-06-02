<?php

namespace App\Jobs;

use App\Services\Invoices\InvoiceService;
use App\Services\MasterInvoices\MasterInvoiceService;

class SubscriptionHandler
{
    use \Illuminate\Foundation\Queue\Queueable;

    public function __construct()
    {
        //
    }

    public function handle(MasterInvoiceService $masterInvoiceService, InvoiceService $invoiceService): void
    {
        foreach ($masterInvoiceService->getOverdues() as $overdue) {
            $invoice = $masterInvoiceService->convertToInvoice($overdue);
            $invoiceService->openAndSendMail($invoice, [
                'days_till_due' => $overdue->days_till_due,
                'performed_when' => $overdue->buildPerformedWhen()]);
            $masterInvoiceService->setNextPrint($overdue);
        }
    }
}
