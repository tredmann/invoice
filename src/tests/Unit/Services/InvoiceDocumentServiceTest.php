<?php

namespace Tests\Unit\Services;

use App\Interfaces\InvoiceInterface;
use App\Services\InvoiceDocuments\InvoiceDocumentService;
use App\Services\Invoices\InvoiceService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class InvoiceDocumentServiceTest extends TestCase
{
    public function testGenerateStoragePath()
    {
        $invoiceService = $this->mock(InvoiceService::class);
        $service = new InvoiceDocumentService($invoiceService);

        $invoice = $this->mock(InvoiceInterface::class);
        $invoice->shouldReceive('getInvoiceNumber')->andReturn('2021-500');
        $invoice->shouldReceive('getInvoiceDate')->andReturn(CarbonImmutable::create('2021-10-10'));

        $filePath = $service->generateStoragePath($invoice);

        self::assertEquals('/inv/'.env('APP_ENV').'/2021/10/2021-500.pdf', $filePath);
    }
}
