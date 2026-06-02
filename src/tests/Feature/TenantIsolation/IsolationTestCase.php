<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CrossTenantMatrix;
use Tests\Concerns\MakesTenants;
use Tests\TestCase;

abstract class IsolationTestCase extends TestCase
{
    use RefreshDatabase;
    use MakesTenants;
    use CrossTenantMatrix;

    protected Tenant $tenantA;

    protected Tenant $tenantB;

    protected User $userA;

    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();
        $this->tenantA = $this->makeTenantWithEverything(['owner' => $this->userA]);
        $this->tenantB = $this->makeTenantWithEverything(['owner' => $this->userB]);
    }

    /** Acts as the user from tenant B trying to reach tenant A's resource (web/session auth). */
    protected function asOutsider(): self
    {
        $this->be($this->userB);

        return $this;
    }

    /** API V1/V2: token auth via Sanctum. */
    protected function asOutsiderApi(): self
    {
        Sanctum::actingAs($this->userB);

        return $this;
    }
}
