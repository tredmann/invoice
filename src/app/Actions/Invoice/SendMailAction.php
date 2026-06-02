<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoices\InvoiceMailAttachmentService;
use App\Services\Invoices\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class SendMailAction
{
    public MessageBag $messages;

    public function __construct(private Invoice $invoice, private Request $request)
    {
        $this->execute();
    }

    public function execute()
    {
        $this->messages = new MessageBag();

        $this->rules();

        if (count($this->messages) > 0) {
            Session::flash('errors', (new ViewErrorBag())->put('default', $this->messages));
            $this->invoice->update(['mail_status' => Invoice::MAIL_STATUS_ERROR]);
        } else {
            $invoiceService = app()->make(InvoiceService::class);
            $invoiceService->sendMail($this->invoice);
            $this->invoice->update(['mail_status' => Invoice::MAIL_STATUS_MAILABLE]);
        }
    }

    public function fails()
    {
        return count($this->messages) > 0;
    }

    public function rules()
    {
        $this->hasInvoiceDocument();
        $this->hasMailReceiver();
        $this->hasGeneralInfo();
        $this->mailAttachmentUploadSuccessfull();
    }

    public function hasInvoiceDocument()
    {
        return $this->invoice->getInvoiceDocument() ?:
            $this->messages->add('invoiceDocument', 'Es konnte kein Rechnungspdf gefunden werden!');
    }

    public function hasMailReceiver()
    {
        return count($this->invoice->customer->customerMailReceivers) > 0 ?:
            $this->messages->add('mailReceiver', 'Dem Kunden wurden keine Mailempfänger hinzugefügt!');
    }

    public function hasGeneralInfo()
    {
        return $this->invoice->customer->tenant->currentGeneralInfo !== null ?:
            $this->messages->add(
                'generalInfo',
                'Zum Tenant dieser Rechnung wurden noch keine Allgemeinen Angaben gemacht!',
            );
    }

    public function mailAttachmentUploadSuccessfull()
    {
        $mailAttachmentInputName = 'mail_attachment';

        if ($this->request->hasFile($mailAttachmentInputName)) {
            $attachment = $this->request->file($mailAttachmentInputName);
            (new InvoiceMailAttachmentService())->saveInvoiceDocument($attachment, $this->invoice);
        }
    }
}
