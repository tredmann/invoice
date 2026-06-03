<?php

declare(strict_types=1);

namespace App\Modules\InvoiceTemplates\Models\Templates;

use App\Models\Invoice;
use App\Models\UniqueNumber;
use App\Services\Invoices\InvoiceService;
use Spatie\LaravelPdf\Facades\Pdf;

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

    public function getView(): string
    {
        return $this->view;
    }

    public function render(array $data): string
    {
        $basePath = tempnam(sys_get_temp_dir(), 'invoice_pdf_');

        if ($basePath === false) {
            throw new \RuntimeException('Failed to allocate tempfile for PDF render');
        }

        $tempPath = $basePath.'.pdf';

        // Pre-create the .pdf file so file_get_contents always has a target.
        // Under Pdf::fake(), FakePdfBuilder::save() does not write to disk;
        // without this the subsequent file_get_contents call would return false.
        // Under the real WeasyPrint driver, save() overwrites this stub.
        touch($tempPath);

        try {
            Pdf::view($this->view, $this->prepareDataForView($data['invoice']))
                ->save($tempPath);

            $bytes = file_get_contents($tempPath);

            if ($bytes === false) {
                throw new \RuntimeException('Failed to read rendered PDF from '.$tempPath);
            }

            return $bytes;
        } finally {
            @unlink($basePath);
            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
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
}
