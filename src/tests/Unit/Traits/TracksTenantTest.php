<?php

namespace Tests\Unit\Traits;

use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TracksTenantTest extends TestCase
{
    use DatabaseTransactions;

    public function testPreservesExplicitTenantIdEvenWhenAnotherTenantIsBoundInContainer(): void
    {
        $owner = User::factory()->create();
        $explicitTenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $boundTenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $boundTenant);

        $customer = Customer::create([
            'tenant_id' => $explicitTenant->id,
            'user_id' => $owner->id,
            'company' => 'Acme',
            'street' => 'Main',
            'postal' => '12345',
            'city' => 'Berlin',
        ]);

        self::assertSame((string) $explicitTenant->id, (string) $customer->fresh()->tenant_id);
    }

    public function testFallsBackToContainerBoundTenantWhenTenantIdMissing(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $tenant);

        $customer = Customer::create([
            'user_id' => $owner->id,
            'company' => 'Acme',
            'street' => 'Main',
            'postal' => '12345',
            'city' => 'Berlin',
        ]);

        self::assertSame((string) $tenant->id, (string) $customer->fresh()->tenant_id);
    }
}
