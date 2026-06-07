<?php

namespace Tests\Feature\Http\Controllers\API\V1;

use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
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
        $this->tenant = Tenant::factory()->create(['owner_id' => $this->user]);
        $this->tenant->users()->attach($this->user);
    }

    public function testIndexGetRoute()
    {
        $this->getJson($this->tenant->route('api.v1.customers.index'))->assertOk();
    }

    public function testShowGetRoute()
    {
        $customer = Customer::factory()->create();

        $this->get(route('api.v1.customers.show', ['customer' => $customer, 'tenant' => $this->tenant]))->assertOk();
    }

    public function testStorePostRoute()
    {
        $request = Customer::factory()
            ->make()
            ->toArray();

        $this->post(route('api.v1.customers.store', ['tenant' => $this->tenant]), $request)->assertCreated();
    }

    public function testDestroyDeleteRoute()
    {
        $customer = Customer::factory()->create();

        $this->delete($this->tenant->route('api.v1.customers.destroy', ['customer' => $customer]))->assertNoContent();
    }

    public function testStoreCustomerWithoutCountryDefaultsToDE(): void
    {
        $payload = [
            'company' => 'Test GmbH',
            'name'    => null,
            'street'  => 'Teststraße 1',
            'postal'  => '12345',
            'city'    => 'Berlin',
            // intentionally omit 'country'
        ];

        $response = $this->postJson(route('api.v1.customers.store', ['tenant' => $this->tenant]), $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('customers', [
            'company' => 'Test GmbH',
            'country' => 'DE',
        ]);
    }
}
