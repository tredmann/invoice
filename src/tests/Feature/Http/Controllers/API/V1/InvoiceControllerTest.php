<?php

namespace Tests\Feature\Http\Controllers\API\V1;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
        $this->customer = Customer::factory()->create();
        $this->invoice = Invoice::factory()
            ->for($this->customer)
            ->create();
    }

    public function testStorePostRoute()
    {
        $request = $this->invoice->toArray();

        $this->postJson(route('api.v1.invoices.store', ['tenant' => $this->tenant]), $request)->assertCreated();
    }

    public function testShowGetRoute()
    {
        $this->getJson(route('api.v1.invoices.lineItems', ['invoice' => $this->invoice, 'tenant' => $this->tenant]))->assertOk();
    }

    public function testDestroyDeleteRoute()
    {
        $this->deleteJson(
            $this->tenant->route('api.v1.invoices.destroy', ['invoice' => $this->invoice]),
        )->assertNoContent();
    }

    public function testNoLineItemOpen()
    {
        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $this->invoice,
        ];

        $this->patchJson($this->tenant->route('api.v1.invoices.open', $request))->assertJsonValidationErrors(
            'lineItems',
        );
    }

    public function testHasLineItemOpen()
    {
        $legalInfo = LegalInfo::factory()->create();
        $generalInfo = GeneralInfo::factory()->create();

        $this->tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();
        $this->tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        LineItem::factory()
            ->for($this->invoice)
            ->create();

        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $this->invoice,
        ];

        $this->patchJson($this->tenant->route('api.v1.invoices.open', $request));

        self::assertEquals($this->invoice->refresh()->status, Invoice::STATUS_OPEN);
    }

    public function testPaid()
    {
        $body = [
            'paid_at' => today()->toDateString(),
        ];

        $this->patchJson($this->tenant->route('api.v1.invoices.paid', ['invoice' => $this->invoice]), $body)->assertJson([
            'data' => ['status' => Invoice::STATUS_PAID],
        ]);
    }
}
