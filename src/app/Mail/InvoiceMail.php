<?php

namespace App\Mail;

use App\Enums\Settings;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Modules\InvoiceTemplates\Models\TemplateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use SerializesModels;
    use Queueable;


    public function __construct(
        private CustomerMailReceiver $customerMailReceiver,
        private Invoice $invoice,
        private InvoiceDocument $invoiceDocument,
        private ?InvoiceDocument $attachmentDocument,
    ) {
    }


    public function build(TemplateManager $templateManager)
    {
        $emailSender = $this->invoice->customer->tenant->getSetting(Settings::EMAIL_SENDER_ADDRESS->value);

        if ($emailSender === null) {
            $emailSender = config('mail.from.address');
        }

        $tenant = $this->invoice->customer->tenant;
        $tenantKey = $tenant->getTenantKey();

        $view = $templateManager->getTemplate(templateKey: 'invoice.email', tenantKey: $tenantKey)->getView();

        $message = $this->from($emailSender)
            ->subject(sprintf('%s %s %s', 'Rechnung', $this->invoice->customer->tenant->currentGeneralInfo->name, today()->format('d.m.Y')))
            ->view($view, [
                'customerMailReceiver' => $this->customerMailReceiver,
                'invoice' => $this->invoice,
                'tenant' => $tenant,
            ])
            ->attachFromStorageDisk($this->invoiceDocument->storage, $this->invoiceDocument->path);

        if ($this->attachmentDocument instanceof \App\Models\InvoiceDocument) {
            $message->attachFromStorageDisk($this->attachmentDocument->storage, $this->attachmentDocument->path);
        }

        return $message;
    }
}
