<?php

namespace Tests\Feature\Http\Controllers\API\V1;

use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MasterInvoiceControllerTest extends TestCase
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
        $this->tenant->users()->attach($this->user);
        $this->customer = Customer::factory()->create();
        $this->masterInvoice = MasterInvoice::factory()
            ->for($this->customer)
            ->create();
    }

    public function testStorePostRoute()
    {
        $request = $this->masterInvoice->toArray();

        $this->postJson($this->tenant->route('api.v1.masterInvoices.store'), $request)->assertCreated();
    }

    public function testShowGetRoute()
    {
        $this->getJson(
            route('api.v1.masterInvoices.masterLineItems', ['masterInvoice' => $this->masterInvoice, 'tenant' => $this->tenant]),
        )->assertOk();
    }

    public function testDestroyDeleteRoute()
    {
        $this->deleteJson(
            $this->tenant->route('api.v1.masterInvoices.destroy', ['masterInvoice' => $this->masterInvoice]),
        )->assertNoContent();
    }

    public function testNoMasterLineItemActivePatchRoute()
    {
        $request = [
            'days_till_due' => $this->faker->randomElement(MasterInvoice::DAYS_TILL_DUE),
            'billing_frequency' => $this->faker->randomElement(MasterInvoice::BILLING_FREQUENCIES),
            'next_print' => now()
                ->addDays($this->faker->randomDigit)
                ->toDateString(),

            'masterInvoice' => $this->masterInvoice->id,
        ];

        $this->patchJson($this->tenant->route('api.v1.masterInvoices.active', $request))->assertJsonValidationErrors(
            'masterLineItems',
        );
    }

    public function testHasMasterLineItemActivePatchRoute()
    {
        MasterLineItem::factory()
            ->for($this->masterInvoice)
            ->create();

        $request = [
            'days_till_due' => $this->faker->randomElement(MasterInvoice::DAYS_TILL_DUE),
            'billing_frequency' => $this->faker->randomElement(MasterInvoice::BILLING_FREQUENCIES),
            'next_print' => now()
                ->addDays($this->faker->randomDigit)
                ->toDateString(),

            'masterInvoice' => $this->masterInvoice->id,
        ];

        $this->patchJson(route('api.v1.masterInvoices.active', ['tenant' => $this->tenant, 'masterInvoice' => $this->masterInvoice]), $request)->assertJson([
            'data' => ['status' => MasterInvoice::STATUS_ACTIVE],
        ]);
    }

    public function testPauseActive()
    {
        $this->masterInvoice->update(['status' => MasterInvoice::STATUS_ACTIVE]);

        $this->patchJson(
            $this->tenant->route('api.v1.masterInvoices.pause', ['masterInvoice' => $this->masterInvoice]),
        )->assertJson([
            'data' => ['status' => MasterInvoice::STATUS_PAUSED],
        ]);
    }
}
