<?php

namespace Tests\Feature\Http\Controllers\API\V2;

use App\Http\Resources\V2\TenantResource;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $this->tenant = Tenant::factory()->create();

        $this->tenant->users()->attach($this->user);
    }

    public function testIndexMethod()
    {
        $response = $this->get('http://localhost:8080/api/v2/tenant/');

        $response->assertStatus(200);

        $tenants = $this->user->tenants;

        $arr = $tenants->map(function ($tenant) {
            return new TenantResource($tenant);
        });

        $response->assertJson($arr->jsonSerialize(), true);
    }

    public function testShowMethod()
    {
        $response = $this->get('http://localhost:8080/api/v2/tenant/'.$this->tenant->id);

        $response->assertStatus(200);

        $tenant = new TenantResource($this->user->tenants->find($this->tenant));

        $response->assertJson($tenant->jsonSerialize(), true);
    }
}
