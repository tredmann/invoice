<?php

namespace App\Actions\Invoice;

use App\Http\Requests\InvoiceStatusOpenRequest;
use App\Models\Invoice;
use App\Services\Invoices\InvoiceService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class OpenInvoiceAction
{
    public MessageBag $messages;

    private Invoice $invoice;

    private InvoiceStatusOpenRequest $request;

    public function __construct(Invoice $invoice, InvoiceStatusOpenRequest $request)
    {
        $this->invoice = $invoice;
        $this->request = $request;

        $this->execute();
    }

    public function execute()
    {
        $this->messages = new MessageBag();

        $this->rules();

        if (count($this->messages) > 0) {
            Session::flash('errors', (new ViewErrorBag())->put('default', $this->messages));
            $this->invoice->update(['status' => Invoice::STATUS_OPEN_PDF_ERROR]);
        } else {
            $invoiceService = app()->make(InvoiceService::class);

            $invoiceService->open($this->invoice, $this->request->validated());
            $this->invoice->update([
                'mail_status' => Invoice::MAIL_STATUS_MAILABLE,
                'status' => Invoice::STATUS_OPEN,
            ]);
        }
    }

    public function fails()
    {
        return count($this->messages) > 0;
    }

    public function rules()
    {
        $this->hasLegalInfo();
        $this->hasGeneralInfo();
    }

    public function hasLegalInfo()
    {
        return $this->invoice->customer->tenant->currentLegalInfo !== null ?:
            $this->messages->add(
                'legalInfo',
                'Zum Tenant dieser Rechnung wurden noch keine Rechtlichen Angaben gemacht!',
            );
    }

    public function hasGeneralInfo()
    {
        return $this->invoice->customer->tenant->currentGeneralInfo !== null ?:
            $this->messages->add(
                'generalInfo',
                'Zum Tenant dieser Rechnung wurden noch keine Allgemeinen Angaben gemacht!',
            );
    }
}
