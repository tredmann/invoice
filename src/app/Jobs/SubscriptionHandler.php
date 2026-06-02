<?php

namespace App\Jobs;

use App\Services\Invoices\InvoiceService;
use App\Services\MasterInvoices\MasterInvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscriptionHandler
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
