<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMailReceiverControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $this->customerMailReceiver = CustomerMailReceiver::factory(['customer_id' => $this->customer->id->toString()])->create();
    }

    public function testCreateGetRoute()
    {
        $this->get(
            route('customerMailReceivers.create', ['customer' => $this->customer, 'tenant' => $this->tenant]),
        )->assertSuccessful();
    }

    public function testGetEditRoute()
    {
        $customerMailReceiver = CustomerMailReceiver::factory()
            ->for($this->customer)
            ->create();

        $this->get(
            route('customerMailReceivers.edit', ['customerMailReceiver' => $customerMailReceiver, 'tenant' => $this->tenant]),
        )->assertSuccessful();
    }

    public function testPatchUpdateRoute()
    {
        $request = CustomerMailReceiver::factory()
            ->make()
            ->toArray();
        $request['customerMailReceiver'] = $this->customerMailReceiver;

        $this->patch($this->tenant->route('customerMailReceivers.update', $request))->assertRedirect(
            $this->tenant->route('customers.mailReceivers', ['customer' => $this->customer]),
        );
    }

    public function testStorePostRoute()
    {
        $request = CustomerMailReceiver::factory(['customer_id' => $this->customer->id->toString()])
            ->make()
            ->toArray();

        $this->post(route('customerMailReceivers.store', ['tenant' => $this->tenant]), $request)->assertRedirect(
            route('customers.mailReceivers', ['customer' => $this->customer, 'tenant' => $this->tenant]),
        );
    }

    public function testDestroyDeleteRoute()
    {
        $this->delete(
            $this->tenant->route('customerMailReceivers.destroy', [
                'customerMailReceiver' => $this->customerMailReceiver,
                'customer' => $this->customer,
            ]),
        )->assertRedirect($this->tenant->route('customers.mailReceivers', ['customer' => $this->customer]));
    }
}
