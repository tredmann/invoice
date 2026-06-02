<?php

namespace Tests\Feature\Actions\Invoice;

use App\Actions\Invoice\SendMailAction;
use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class SendMailActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testFailsWhenInvoiceHasNoInvoiceDocument(): void
    {
        $invoice = $this->buildOpenInvoice(
            withGeneralInfo: true,
            withMailReceiver: true,
            withInvoiceDocument: false,
        );

        $action = new SendMailAction($invoice, new Request());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('invoiceDocument'));
        self::assertSame(Invoice::MAIL_STATUS_ERROR, $invoice->fresh()->mail_status);
    }

    public function testFailsWhenCustomerHasNoMailReceivers(): void
    {
        $invoice = $this->buildOpenInvoice(
            withGeneralInfo: true,
            withMailReceiver: false,
            withInvoiceDocument: true,
        );

        $action = new SendMailAction($invoice, new Request());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('mailReceiver'));
        self::assertSame(Invoice::MAIL_STATUS_ERROR, $invoice->fresh()->mail_status);
    }

    public function testFailsWhenTenantHasNoGeneralInfo(): void
    {
        $invoice = $this->buildOpenInvoice(
            withGeneralInfo: false,
            withMailReceiver: true,
            withInvoiceDocument: true,
        );

        $action = new SendMailAction($invoice, new Request());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('generalInfo'));
        self::assertSame(Invoice::MAIL_STATUS_ERROR, $invoice->fresh()->mail_status);
    }

    public function testAccumulatesAllGuardErrorsWhenNothingIsConfigured(): void
    {
        $invoice = $this->buildOpenInvoice(
            withGeneralInfo: false,
            withMailReceiver: false,
            withInvoiceDocument: false,
        );

        $action = new SendMailAction($invoice, new Request());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('invoiceDocument'));
        self::assertTrue($action->messages->has('mailReceiver'));
        self::assertTrue($action->messages->has('generalInfo'));
    }

    private function buildOpenInvoice(
        bool $withGeneralInfo,
        bool $withMailReceiver,
        bool $withInvoiceDocument,
    ): Invoice {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $tenant->users()->attach($owner);

        if ($withGeneralInfo) {
            $tenant->currentGeneralInfo()->associate(GeneralInfo::factory()->create())->save();
        }

        $customer = Customer::factory(['tenant_id' => $tenant->id, 'user_id' => $owner->id])->create();

        if ($withMailReceiver) {
            CustomerMailReceiver::factory(['customer_id' => $customer->id])->create();
        }

        $invoice = Invoice::factory()
            ->for($customer)
            ->open()
            ->withMailStatus(Invoice::MAIL_STATUS_MAILABLE)
            ->create();

        if ($withInvoiceDocument) {
            InvoiceDocument::factory()->for($invoice)->invoiceDocument()->create();
        }

        return $invoice;
    }
}
