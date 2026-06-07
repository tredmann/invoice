<?php

namespace Tests\Feature\Http\Controllers;

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

        $this->user = User::factory()->create([
            'is_admin' => true,
        ]);
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
    }

    public function testIndexGetRoute()
    {
        $this->get($this->tenant->route('customers.index'))->assertSuccessful();
    }

    public function testCreateGetRoute()
    {
        $this->get($this->tenant->route('customers.create'))->assertSuccessful();
    }

    public function testStorePostRoute()
    {
        $customers = Customer::count();

        $request = Customer::factory()
            ->make([
                'tenant_id' => $this->tenant->id,
            ])
            ->toArray();

        $data = [
            'company' => $request['company'],
            'name' => $request['name'],
            'street' => $request['street'],
            'postal' => $request['postal'],
            'city' => $request['city'],
            'country' => $request['country'],
        ];

        $this->actingAs($this->user, 'web')->post('http://localhost:8080/'.$this->tenant->id.'/customers', $request);

        $this->assertDatabaseCount('customers', $customers + 1);
        $this->assertDatabaseHas('customers', $data);
    }

    public function testInvoicesGetRoute()
    {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->get($this->tenant->route('customers.invoices', ['customer' => $customer]))->assertSuccessful();
    }

    public function testMasterInvoicesGetRoute()
    {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->get($this->tenant->route('customers.invoices', ['customer' => $customer]))->assertSuccessful();
    }

    public function testMailReceiversGetRoute()
    {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->get($this->tenant->route('customers.mailReceivers', ['customer' => $customer]))->assertSuccessful();
    }

    public function testShowGetRoute()
    {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->get($this->tenant->route('customers.show', ['customer' => $customer]))->assertSuccessful();
    }

    public function testDestroyDeleteRoute()
    {
        $customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->delete($this->tenant->route('customers.destroy', ['customer' => $customer]))->assertRedirect(
            $this->tenant->route('customers.index'),
        );
    }

    public function testStorePostRouteWithCountryDE()
    {
        $request = Customer::factory()
            ->make([
                'tenant_id' => $this->tenant->id,
                'country' => 'DE',
            ])
            ->toArray();

        $this->actingAs($this->user, 'web')
            ->post('http://localhost:8080/'.$this->tenant->id.'/customers', $request)
            ->assertRedirect();

        $this->assertDatabaseHas('customers', ['country' => 'DE', 'company' => $request['company']]);
    }

    public function testStorePostRouteWithoutCountryDefaultsToDE()
    {
        $request = Customer::factory()
            ->make(['tenant_id' => $this->tenant->id])
            ->toArray();

        unset($request['country']);

        $this->actingAs($this->user, 'web')
            ->post('http://localhost:8080/'.$this->tenant->id.'/customers', $request)
            ->assertRedirect();

        $this->assertDatabaseHas('customers', ['country' => 'DE', 'company' => $request['company']]);
    }

    public function testStorePostRouteFailsWhenCountryInvalid()
    {
        $request = Customer::factory()
            ->make(['tenant_id' => $this->tenant->id])
            ->toArray();

        $request['country'] = 'US';

        $this->actingAs($this->user, 'web')
            ->post('http://localhost:8080/'.$this->tenant->id.'/customers', $request)
            ->assertSessionHasErrors('country');
    }
}
