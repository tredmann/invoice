<?php

namespace Tests\Unit\Policies;

use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Policies\LegalInfoPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegalInfoPolicyTest extends TestCase
{
    use DatabaseTransactions;

    public function testIsFirstReturnsTrueWhenBoundTenantHasNoCurrentLegalInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $tenant);

        self::assertTrue((new LegalInfoPolicy())->isFirst($owner));
    }

    public function testIsFirstReturnsFalseWhenBoundTenantHasCurrentLegalInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $legalInfo = LegalInfo::factory()->create();
        $tenant->currentLegalInfo()->associate($legalInfo)->save();
        app()->instance(Tenant::class, $tenant->fresh('currentLegalInfo'));

        self::assertFalse((new LegalInfoPolicy())->isFirst($owner));
    }

    public function testIsFirstWithTenantReturnsTrueWhenGivenTenantHasNoCurrentLegalInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();

        self::assertTrue((new LegalInfoPolicy())->isFirstWithTenant($owner, $tenant));
    }

    public function testIsFirstWithTenantReturnsFalseWhenGivenTenantHasCurrentLegalInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $legalInfo = LegalInfo::factory()->create();
        $tenant->currentLegalInfo()->associate($legalInfo)->save();

        self::assertFalse((new LegalInfoPolicy())->isFirstWithTenant($owner, $tenant->fresh('currentLegalInfo')));
    }
}
