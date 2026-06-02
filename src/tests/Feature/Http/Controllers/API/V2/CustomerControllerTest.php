<?php

namespace Tests\Feature\Http\Controllers\API\V2;

use App\Models\Customer;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\Customers\CustomerService;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $this->tenant = Tenant::factory()->create();
        $this->tenant->users()->attach($this->user);

        $this->customerService = new CustomerService();

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

        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function testItWillRetrieveAllCustomers()
    {
        $response = $this->get(route('api.v2.tenants.customers', ['tenant' => $this->tenant]));

        $response->assertStatus(200);

        $customers = $this->tenant->customers;

        $arr = $customers->map(function ($customer) {
            return $this->customerService->toArray($customer);
        });

        $response->assertJson($arr->toArray());
    }

    public function testItWillRetrieveSingleCustomer()
    {
        $response = $this->get(route('api.v2.tenants.customers.show', [
            'tenant' => $this->tenant,
            'customer' => $this->customer,
        ]));

        $response->assertStatus(200);

        $customer = Customer::find($this->customer->id);

        $response->assertJson($this->customerService->toArray($customer));
    }
}
