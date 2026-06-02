<?php

namespace Tests\Feature\Http\Controllers;

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
        $this->customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->masterInvoice = MasterInvoice::factory()
            ->for($this->customer)
            ->create();
    }

    public function testStorePostRoute()
    {
        $this->post(route('masterInvoices.store', ['customer' => $this->customer, 'tenant' => $this->tenant]))->assertRedirect();
    }

    public function testShowGetRoute()
    {
        $this->get(
            route('masterInvoices.masterLineItems', ['masterInvoice' => $this->masterInvoice, 'tenant' => $this->tenant]),
        )->assertSuccessful();
    }

    public function testActivateGetRoute()
    {
        $this->get(
            $this->tenant->route('masterInvoices.activate', ['masterInvoice' => $this->masterInvoice]),
        )->assertSuccessful();
    }

    public function testDestroyDeleteRoute()
    {
        $this->delete(
            $this->tenant->route('masterInvoices.destroy', ['masterInvoice' => $this->masterInvoice]),
        )->assertRedirect($this->tenant->route('customers.masterInvoices', ['customer' => $this->customer]));
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

        $this->patch($this->tenant->route('masterInvoices.active', $request))->assertSessionHasErrors(
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

        $this->patch($this->tenant->route('masterInvoices.active', $request))->assertSessionHasAll(['success']);
    }

    public function testPauseActive()
    {
        $this->masterInvoice->update(['status' => MasterInvoice::STATUS_ACTIVE]);

        $this->patch(
            $this->tenant->route('masterInvoices.pause', ['masterInvoice' => $this->masterInvoice]),
        )->assertSessionHasAll(['success']);
    }
}
