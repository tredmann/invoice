<?php

namespace Tests\Feature\Concerns;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CrossTenantMatrix;
use Tests\TestCase;

class CrossTenantMatrixTest extends TestCase
{
    use RefreshDatabase;
    use CrossTenantMatrix;

    public function testRejectsCrossTenantOwnerOnlyRoute(): void
    {
        /** @var User $ownerA */
        $ownerA = User::factory()->create();
        /** @var Tenant $tenantA */
        $tenantA = Tenant::factory(['owner_id' => $ownerA->id])->create();
        $tenantA->users()->attach($ownerA);

        /** @var User $outsider */
        $outsider = User::factory()->create();

        $this->be($outsider);

        // tenants.edit is gated by `can:isOwnerViewGate,tenant` in routes/tenant/tenants.php.
        // A non-owner must be rejected (401/403/404). Used here as a proof-of-API for
        // the CrossTenantMatrix trait; a wider Phase 2 sweep will cover every endpoint.
        $this->assertEndpointRejectsCrossTenant(
            'GET',
            fn () => route('tenants.edit', ['tenant' => $tenantA])
        );
    }
}
