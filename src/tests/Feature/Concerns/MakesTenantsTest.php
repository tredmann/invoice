<?php

namespace Tests\Feature\Concerns;

use App\Models\Tenant\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

class MakesTenantsTest extends TestCase
{
    use RefreshDatabase;
    use MakesTenants;

    public function testReturnsFullyWiredTenant(): void
    {
        $tenant = $this->makeTenantWithEverything();

        self::assertInstanceOf(Tenant::class, $tenant);
        self::assertNotNull($tenant->owner_id);
        self::assertNotNull($tenant->currentGeneralInfo, 'expected current general info');
        self::assertNotNull($tenant->currentLegalInfo, 'expected current legal info');
        self::assertGreaterThan(0, $tenant->customers->count(), 'expected at least one customer');
        self::assertGreaterThan(
            0,
            $tenant->customers->first()->customerMailReceivers->count(),
            'expected at least one mail receiver on the customer'
        );
        self::assertTrue($tenant->users->contains($tenant->owner), 'owner should be attached as a tenant user');
        self::assertGreaterThanOrEqual(6, $tenant->settings->count(), 'expected seeded settings (sender + 5 SMTP keys)');
    }
}
