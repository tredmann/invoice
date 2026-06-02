<?php

namespace Tests\Feature\Actions\Invoice;

use App\Actions\Invoice\OpenInvoiceAction;
use App\Http\Requests\InvoiceStatusOpenRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OpenInvoiceActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testFailsWhenTenantHasNoLegalInfo(): void
    {
        $invoice = $this->buildDraftInvoice(withGeneralInfo: true, withLegalInfo: false);

        $action = new OpenInvoiceAction($invoice, new InvoiceStatusOpenRequest());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('legalInfo'));
        self::assertSame(Invoice::STATUS_OPEN_PDF_ERROR, $invoice->fresh()->status);
    }

    public function testFailsWhenTenantHasNoGeneralInfo(): void
    {
        $invoice = $this->buildDraftInvoice(withGeneralInfo: false, withLegalInfo: true);

        $action = new OpenInvoiceAction($invoice, new InvoiceStatusOpenRequest());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('generalInfo'));
        self::assertSame(Invoice::STATUS_OPEN_PDF_ERROR, $invoice->fresh()->status);
    }

    public function testAccumulatesBothGuardErrorsWhenTenantIsBare(): void
    {
        $invoice = $this->buildDraftInvoice(withGeneralInfo: false, withLegalInfo: false);

        $action = new OpenInvoiceAction($invoice, new InvoiceStatusOpenRequest());

        self::assertTrue($action->fails());
        self::assertTrue($action->messages->has('legalInfo'));
        self::assertTrue($action->messages->has('generalInfo'));
    }

    private function buildDraftInvoice(bool $withGeneralInfo, bool $withLegalInfo): Invoice
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $tenant->users()->attach($owner);

        if ($withGeneralInfo) {
            $tenant->currentGeneralInfo()->associate(GeneralInfo::factory()->create())->save();
        }
        if ($withLegalInfo) {
            $tenant->currentLegalInfo()->associate(LegalInfo::factory()->create())->save();
        }

        $customer = Customer::factory(['tenant_id' => $tenant->id, 'user_id' => $owner->id])->create();

        return Invoice::factory()->for($customer)->create();
    }
}
