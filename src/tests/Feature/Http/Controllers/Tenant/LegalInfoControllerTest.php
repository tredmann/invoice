<?php

namespace Tests\Feature\Http\Controllers\Tenant;

use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LegalInfoControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->isAdmin()
            ->create();
        Sanctum::actingAs($this->user, ['*']);
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
    }

    public function testIndexGetRoute()
    {
        $this->get(route('legalInfos.index', ['tenant' => $this->tenant]))->assertSuccessful();
    }

    public function testCreateGetRoute()
    {
        $this->get(route('legalInfos.create', ['tenant' => $this->tenant]))->assertSuccessful();
    }

    public function testStorePostRoute()
    {
        $request = LegalInfo::factory()
            ->make()
            ->toArray();
        $this->be($this->user);

        $response = $this->post(route('legalInfos.store', ['tenant' => $this->tenant]), $request);
        $response->assertRedirect(
            route('legalInfos.index', ['tenant' => $this->tenant]),
        );
    }

    public function testStorePostRouteMissingAllInput()
    {
        $this->post(route('legalInfos.store', ['tenant' => $this->tenant]))->assertSessionHasErrors();
    }

    public function testStorePostRouteExistingLegalInfoOnTenant()
    {
        $request = LegalInfo::factory()
            ->make()
            ->toArray();

        $this->post(route('legalInfos.store', ['tenant' => $this->tenant]), $request)->assertRedirect(
            $this->tenant->route('legalInfos.index'),
        );

        $request = LegalInfo::factory()
            ->make()
            ->toArray();

        $this->post(route('legalInfos.store', ['tenant' => $this->tenant]), $request)->assertForbidden();
    }

    public function testEditGetRoute()
    {
        $legalInfo = LegalInfo::factory()->create();

        $this->get($this->tenant->route('legalInfos.edit', ['legalInfo' => $legalInfo]))->assertSuccessful();
    }

    public function testUpdatePatchRoute()
    {
        $request = LegalInfo::factory()
            ->for($this->tenant)
            ->make()
            ->toArray();

        $request['legalInfo'] = LegalInfo::factory()->create();

        $this->patch($this->tenant->route('legalInfos.update', $request))->assertRedirect(
            $this->tenant->route('legalInfos.index'),
        );
    }

    public function testUpdatePatchRouteNotFound()
    {
        $this->patch($this->tenant->route('legalInfos.update', ['legalInfo' => 1]))->assertNotFound();
    }

    public function testDestroyDeleteRoute()
    {
        $legalInfo = LegalInfo::factory()->create();

        $this->delete($this->tenant->route('legalInfos.destroy', ['legalInfo' => $legalInfo]))->assertRedirect(
            $this->tenant->route('legalInfos.index'),
        );
    }

    public function testDestroyDeleteRouteNotFound()
    {
        $this->delete($this->tenant->route('legalInfos.destroy', ['legalInfo' => 1]))->assertNotFound();
    }
}
