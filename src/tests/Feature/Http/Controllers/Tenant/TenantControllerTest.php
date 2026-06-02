<?php

namespace Tests\Feature\Http\Controllers\Tenant;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($this->user, ['*']);
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
    }

    public function testStore()
    {
        $tenantCount = Tenant::count();
        $request = Tenant::factory()
            ->make()
            ->toArray();

        $this->be($this->user); // log in the user again
        $this->post(route('tenants.store'), $request)->assertRedirect();

        $this->assertDatabaseCount('tenants', $tenantCount + 1);
    }

    public function testStoreTenantTwice()
    {
        $tenantCount = Tenant::count();
        $otherTenant = Tenant::factory()->create();

        $request = Tenant::factory()
            ->make()
            ->toArray();

        $request['name'] = $otherTenant->name;

        $this->post($this->tenant->route('tenants.store', $request))->assertSessionHasErrors('name');

        $this->assertDatabaseCount('tenants', $tenantCount + 1);
    }

    public function testUpdate()
    {
        $request = Tenant::factory()
            ->make()
            ->toArray();

        $this->patch($this->tenant->route('tenants.update'), $request)->assertRedirect(
            $this->tenant->refresh()->route('tenants.show'),
        );

        $this->assertDatabaseHas('tenants', ['id' => $this->tenant->id, 'name' => $request['name']]);
    }

    public function testUpdateToOtherTenantName()
    {
        $otherTenant = Tenant::factory()->create();

        $request = Tenant::factory()
            ->make()
            ->toArray();

        $request['name'] = $otherTenant->name;

        $this->patch($this->tenant->route('tenants.update', $request))->assertSessionHasErrors('name');

        $this->assertDatabaseHas('tenants', ['id' => $this->tenant->id, 'name' => $this->tenant->name]);
    }

    public function testEdit()
    {
        $this->get($this->tenant->route('tenants.edit'))->assertSuccessful();
    }

    public function testInviteUserForm()
    {
        $this->get($this->tenant->route('tenants.invite-user-form'))->assertSuccessful();
    }

    public function testInviteUser()
    {
        $registeredUser = User::factory()->create();

        $request = [
            'email' => $registeredUser->email,
        ];

        $this->patch($this->tenant->route('tenants.invite-user', $request))->assertRedirect(
            $this->tenant->route('tenants.users'),
        );

        self::assertSame($this->tenant->users->count(), 2);
    }

    public function testRemoveUser()
    {
        $addedUser = User::factory()->create();
        $this->tenant->users()->attach($addedUser);

        $this->patch($this->tenant->route('tenants.remove-user', ['user' => $addedUser]))->assertRedirect(
            $this->tenant->route('tenants.users'),
        );
    }

    public function testDestroy()
    {
        $this->delete($this->tenant->route('tenants.destroy'))->assertRedirect(route('tenants.index'));
    }

    public function testIndex()
    {
        $this->get($this->tenant->route('tenants.index'))->assertSuccessful();
    }

    public function testCreate()
    {
        $this->get($this->tenant->route('tenants.create'))->assertSuccessful();
    }

    public function testShow()
    {
        $this->get($this->tenant->route('tenants.show'))->assertSuccessful();
    }

    public function testUsers()
    {
        $this->get($this->tenant->route('tenants.users'))->assertSuccessful();
    }

    public function testShowAsNonOwner()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get($this->tenant->route('tenants.show'))
            ->assertNotFound();
    }

    public function testShowAsTenantMember()
    {
        $user = User::factory()->create();
        $this->tenant->users()->attach($user);

        $this->actingAs($user)
            ->get($this->tenant->route('tenants.show'))
            ->assertForbidden();
    }
}
