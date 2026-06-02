<?php

namespace Tests\Feature\Http\Controllers\Tenant;

use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GeneralInfoControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->isAdmin()
            ->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
    }

    public function testIndexGetRoute()
    {
        $this->get(route('generalInfos.index', ['tenant' => $this->tenant]))->assertSuccessful();
    }

    public function testCreateGetRoute()
    {
        $this->get($this->tenant->route('generalInfos.create'))->assertSuccessful();
    }

    public function testStorePostRoute()
    {
        $request = GeneralInfo::factory()
            ->make()
            ->toArray();

        $this->post($this->tenant->route('generalInfos.store', $request))->assertRedirect(
            $this->tenant->route('generalInfos.index'),
        );
    }

    public function testStorePostRouteMissingAllInput()
    {
        $this->post($this->tenant->route('generalInfos.store'))->assertSessionHasErrors();
    }

    public function testStorePostRouteExistingGeneralInfoOnTenant()
    {
        $request = GeneralInfo::factory()
            ->make()
            ->toArray();

        $this->post($this->tenant->route('generalInfos.store', $request))->assertRedirect(
            $this->tenant->route('generalInfos.index'),
        );

        $request = GeneralInfo::factory()
            ->make()
            ->toArray();

        $this->post($this->tenant->route('generalInfos.store', $request))->assertRedirect(
            $this->tenant->route('generalInfos.index'),
        );
    }

    public function testEditGetRoute()
    {
        $generalInfo = GeneralInfo::factory()->create();

        $this->get($this->tenant->route('generalInfos.edit', ['generalInfo' => $generalInfo]))->assertSuccessful();
    }

    public function testUpdatePatchRoute()
    {
        $request = GeneralInfo::factory()
            ->for($this->tenant)
            ->make()
            ->toArray();

        $request['generalInfo'] = GeneralInfo::factory()->create();

        $this->patch($this->tenant->route('generalInfos.update', $request))->assertRedirect(
            $this->tenant->route('generalInfos.index'),
        );
    }

    public function testUpdatePatchRouteNotFound()
    {
        $this->patch($this->tenant->route('generalInfos.update', ['generalInfo' => 1]))->assertNotFound();
    }

    public function testDestroyDeleteRoute()
    {
        $generalInfo = GeneralInfo::factory()->create();

        $this->delete($this->tenant->route('generalInfos.destroy', ['generalInfo' => $generalInfo]))->assertRedirect(
            $this->tenant->route('generalInfos.index'),
        );
    }

    public function testDestroyDeleteRouteNotFound()
    {
        $this->delete($this->tenant->route('generalInfos.destroy', ['generalInfo' => 1]))->assertNotFound();
    }
}
