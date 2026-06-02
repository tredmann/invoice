<?php

namespace App\Modules\InvoiceTemplates\Models\Templates;

use App\Models\Invoice;
use App\Models\UniqueNumber;
use App\Services\Invoices\InvoiceService;
use Barryvdh\DomPDF\PDF;

class BladeInvoiceTemplate implements Template
{
    /**
     * @param string $tenant The tenant for which the view should be loaded
     * @param string $view The string of the view which can be loaded by blade
     */
    public function __construct(
        private readonly string $tenant,
        private readonly string $view,
    ) {
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function render(array $data): string
    {
        $pdfRenderer = app(PDF::class);

        $pdf = $pdfRenderer->loadView(
            view: $this->view,
            data: $this->prepareDataForView($data['invoice'])
        );
        return $pdf->output();
    }

    /**
     * Prepares the data based on the invoice to inject into the template
     */
    private function prepareDataForView(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'legalInfo' => $invoice->customer->tenant->currentLegalInfo,
            'generalInfo' => $invoice->customer->tenant->currentGeneralInfo,
            'totalPerTax' => InvoiceService::totalPerTax($invoice->lineItems),
            'uniqueNumber' => new UniqueNumber(),
        ];
    }

    public function getView(): string
    {
        return $this->view;
    }
}
