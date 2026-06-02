<?php

namespace Tests\Feature\Services\Invoices;

use App\Jobs\GeneratePDF;
use App\Jobs\SendInvoiceMail;
use App\Models\Invoice;
use App\Services\Invoices\InvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class OpenAndSendMailChainTest extends TestCase
{
    use DatabaseTransactions;
    use MakesTenants;

    public function testOpenAndSendMailDispatchesGeneratePdfThenSendMail(): void
    {
        Bus::fake();

        $tenant = $this->makeTenantWithEverything();
        $customer = $tenant->customers->first();
        $invoice = Invoice::factory()->for($customer)->create();

        app(InvoiceService::class)->openAndSendMail($invoice, [
            'days_till_due' => 14,
            'performed_when' => 'June 2026',
        ]);

        Bus::assertChained([
            GeneratePDF::class,
            SendInvoiceMail::class,
        ]);

        self::assertSame(Invoice::STATUS_OPEN, $invoice->fresh()->status);
    }
}
