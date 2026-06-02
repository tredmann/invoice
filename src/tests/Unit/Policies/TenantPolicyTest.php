<?php

namespace Tests\Unit\Policies;

use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Policies\TenantPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TenantPolicyTest extends TestCase
{
    use DatabaseTransactions;

    public function testIsOwnerReturnsTrueWhenContainerBoundTenantIsOwnedByUser(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $tenant);

        self::assertTrue((new TenantPolicy())->isOwner($owner));
    }

    public function testIsOwnerReturnsFalseWhenContainerBoundTenantIsNotOwnedByUser(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $tenant);

        self::assertFalse((new TenantPolicy())->isOwner($other));
    }

    public function testIsOwnerViewGateReturnsTrueForOwnerOfGivenTenant(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();

        self::assertTrue((new TenantPolicy())->isOwnerViewGate($owner, $tenant->fresh('owner')));
    }

    public function testIsOwnerViewGateReturnsFalseForNonOwnerOfGivenTenant(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();

        self::assertFalse((new TenantPolicy())->isOwnerViewGate($other, $tenant->fresh('owner')));
    }
}
