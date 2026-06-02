<?php

namespace Tests\Unit\Policies;

use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Policies\GeneralInfoPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GeneralInfoPolicyTest extends TestCase
{
    use DatabaseTransactions;

    public function testIsFirstReturnsTrueWhenBoundTenantHasNoCurrentGeneralInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        app()->instance(Tenant::class, $tenant);

        self::assertTrue((new GeneralInfoPolicy())->isFirst($owner));
    }

    public function testIsFirstReturnsFalseWhenBoundTenantHasCurrentGeneralInfo(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $generalInfo = GeneralInfo::factory()->create();
        $tenant->currentGeneralInfo()->associate($generalInfo)->save();
        app()->instance(Tenant::class, $tenant->fresh('currentGeneralInfo'));

        self::assertFalse((new GeneralInfoPolicy())->isFirst($owner));
    }
}
