<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->isAdmin()
            ->create();
        $this->be($this->user);
    }

    public function testIndexGetRoute()
    {
        $this->get(route('admin.user-panel.index'))->assertSuccessful();
    }

    public function testCreateGetRoute()
    {
        $this->get(route('admin.user-panel.create'))->assertSuccessful();
    }

    public function testStorePostRoute()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => ($password = $this->faker->password(8)),
            'password_confirmation' => $password,
        ];

        $this->post(route('admin.user-panel.store', $request))->assertRedirect(route('admin.user-panel.index'));
        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    }

    public function testStorePostRouteMissingAllInput()
    {
        $this->post(route('admin.user-panel.store'))->assertSessionHasErrors();
    }

    public function testStorePostRouteExistingEmail()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->user->email,
            'password' => ($password = $this->faker->password(8)),
            'password_confirmation' => $password,
        ];

        $this->post(route('admin.user-panel.store', $request))->assertSessionHasErrors('email');
    }

    public function testStorePostRouteConfirmedPassword()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => $this->faker->password(8),
            'password_confirmation' => $this->faker->password(8),
        ];

        $this->post(route('admin.user-panel.store', $request))->assertSessionHasErrors('password');
    }

    public function testStorePostRoutePasswordLessThan8()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => ($password = $this->faker->password(0, 4)),
            'password_confirmation' => $password,
        ];

        $this->post(route('admin.user-panel.store', $request))->assertSessionHasErrors('password');
    }

    public function testEditGetRoute()
    {
        $this->get(route('admin.user-panel.edit', ['user' => $this->user]))->assertSuccessful();
    }

    public function testUpdatePatchRouteAllNew()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => ($password = $this->faker->password(8)),
            'password_confirmation' => $password,
        ];

        $request['user'] = $this->user;

        $this->patch(route('admin.user-panel.update', $request))->assertRedirect(route('admin.user-panel.index'));
        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    }

    public function testUpdatePatchRouteNewPassword()
    {
        $request = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => ($password = $this->faker->password(8)),
            'password_confirmation' => $password,
        ];

        $request['user'] = $this->user;

        $this->patch(route('admin.user-panel.update', $request))->assertRedirect(route('admin.user-panel.index'));
        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    }

    public function testUpdatePatchRouteNewName()
    {
        $request = [
            'name' => $this->faker->name,
            'email' => $this->user->email,
        ];

        $request['user'] = $this->user;

        $this->patch(route('admin.user-panel.update', $request))->assertRedirect(route('admin.user-panel.index'));
        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    }

    public function testUpdatePatchRouteNotFound()
    {
        $this->patch(route('admin.user-panel.update', ['user' => Str::uuid()]))->assertNotFound();
    }

    public function testDemoteAdminPatchRouteSuccess()
    {
        $this->patch(
            route(
                'admin.user-panel.demote',
                User::factory()
                    ->isAdmin()
                    ->create(),
            ),
        )->assertRedirect(route('admin.user-panel.index'));
    }

    public function testDemoteUserPatchRouteFailure()
    {
        $this->patch(route('admin.user-panel.demote', User::factory()->create()))->assertSessionHas('error');
    }

    public function testPromoteUserPatchRouteSuccess()
    {
        $this->patch(route('admin.user-panel.promote', User::factory()->create()))->assertRedirect(
            route('admin.user-panel.index'),
        );
    }

    public function testPromoteUserPatchRouteFailure()
    {
        $this->patch(
            route(
                'admin.user-panel.promote',
                User::factory()
                    ->isAdmin()
                    ->create(),
            ),
        )->assertSessionHas('error');
    }

    public function testDestroyDeleteRoute()
    {
        $user = User::factory()->create();

        $this->delete(route('admin.user-panel.destroy', ['user' => $user]))->assertRedirect(
            route('admin.user-panel.index'),
        );
    }

    public function testDestroyDeleteRouteNotFound()
    {
        $this->delete(route('admin.user-panel.destroy', ['user' => Str::uuid()]))->assertNotFound();
    }
}
