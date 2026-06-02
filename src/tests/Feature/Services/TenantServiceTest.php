<?php

namespace Tests\Feature\Services;

use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testStoreCreatesTenantOwnedByAuthenticatedUserAndAttachesThem(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tenant = (new TenantService())->store(['name' => 'Acme Corp']);

        self::assertSame('Acme Corp', $tenant->name);
        self::assertSame('acme-corp', $tenant->slug);
        self::assertSame($user->id, $tenant->owner_id);
        self::assertTrue($tenant->users()->where('users.id', $user->id)->exists());
    }

    public function testUpdateRewritesSlugFromName(): void
    {
        $tenant = Tenant::factory([
            'owner_id' => User::factory()->create()->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
        ])->create();

        (new TenantService())->update(['name' => 'New Company'], $tenant);

        $tenant->refresh();
        self::assertSame('New Company', $tenant->name);
        self::assertSame('new-company', $tenant->slug);
    }

    public function testAddUserAttachesExistingUserByEmail(): void
    {
        $owner = User::factory()->create();
        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $invitee = User::factory(['email' => 'invitee@example.test'])->create();

        (new TenantService())->addUser(['email' => 'invitee@example.test'], $tenant);

        self::assertTrue($tenant->users()->where('users.id', $invitee->id)->exists());
    }
}
