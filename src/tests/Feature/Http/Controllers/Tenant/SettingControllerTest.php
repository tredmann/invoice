<?php

namespace Tests\Feature\Http\Controllers\Tenant;

use App\Models\Setting;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->isAdmin()
            ->create();

        $this->be($this->user);
        $this->tenant = Tenant::factory()->create(['owner_id' => $this->user]);
        $this->tenant->users()->attach($this->user);
        $this->setting = Setting::factory()
            ->for($this->tenant)
            ->create();
    }

    public function testIndexGetRoute()
    {
        $this->get(route('settings.index', ['tenant' => $this->tenant]))->assertSuccessful();
    }

    public function testCreateGetRoute()
    {
        $this->get($this->tenant->route('settings.create'))->assertSuccessful();
    }

    public function testStorePostRoute()
    {
        $request = Setting::factory()
            ->make()
            ->toArray();

        $this->post($this->tenant->route('settings.store', $request))->assertRedirect(
            $this->tenant->route('settings.index'),
        );
    }

    public function testStorePostRouteMissingAllInput()
    {
        $this->post($this->tenant->route('settings.store'))->assertSessionHasErrors();
    }

    public function testStorePostRouteSameTenantDuplicateKeys()
    {
        $settingCount = Setting::count();
        $request = Setting::factory()
            ->make(['key' => 'test'])
            ->toArray();

        $this->post($this->tenant->route('settings.store'), $request)->assertRedirect(
            $this->tenant->route('settings.index'),
        );

        $settingCount = $settingCount + 1;
        $this->assertDatabaseCount('settings', $settingCount);
        $last = Setting::orderBy('created_at', 'desc')->first();

        $request1 = Setting::factory()
            ->make(['key' => 'test'])
            ->toArray();

        $this->post(route('settings.store', ['tenant' => $this->tenant]), $request1);
        $this->assertEquals($last, Setting::orderBy('created_at', 'desc')->first());
        $this->assertDatabaseCount('settings', $settingCount);
    }

    public function testStorePostRouteDifferentTenantSameKeys()
    {
        $request = Setting::factory()
            ->make(['key' => 'test'])
            ->toArray();

        $this->post($this->tenant->route('settings.store', $request))->assertRedirect(
            $this->tenant->route('settings.index'),
        );

        $tenant2 = Tenant::factory()->create(['owner_id' => $this->user]);

        $tenant2->users()->attach($this->user);

        $request2 = Setting::factory()
            ->for($tenant2)
            ->make(['key' => 'test'])
            ->toArray();

        $this->post($tenant2->route('settings.store', $request2))->assertRedirect($tenant2->route('settings.index'));
    }

    public function testEditGetRoute()
    {
        $this->get($this->tenant->route('settings.edit', ['setting' => $this->setting]))->assertSuccessful();
    }

    public function testUpdatePatchRoute()
    {
        $request = Setting::factory()
            ->for($this->tenant)
            ->make()
            ->toArray();

        $request['setting'] = $this->setting;

        $this->patch($this->tenant->route('settings.update', $request))->assertRedirect(
            $this->tenant->route('settings.index'),
        );
    }

    public function testUpdatePatchRouteNotFound()
    {
        $this->patch($this->tenant->route('settings.update', ['setting' => 1]))->assertNotFound();
    }

    public function testUpdatePatchRouteChangeValueAndTypeButNotKey()
    {
        $request = Setting::factory()
            ->for($this->tenant)
            ->make(['key' => $this->setting->key])
            ->toArray();

        $request['setting'] = $this->setting;

        $this->patch($this->tenant->route('settings.update', $request))->assertRedirect(
            $this->tenant->route('settings.index'),
        );
    }

    public function testDestroyDeleteRoute()
    {
        $setting = Setting::factory()->for($this->tenant)->create();

        $this->delete($this->tenant->route('settings.destroy', ['setting' => $setting]))->assertRedirect(
            $this->tenant->route('settings.index'),
        );
    }

    public function testDestroyDeleteRouteNotFound()
    {
        $this->delete($this->tenant->route('settings.destroy', ['setting' => 1]))->assertNotFound();
    }
}
