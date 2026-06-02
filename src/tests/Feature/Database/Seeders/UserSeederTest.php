<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_only_one_admin_user_with_env_email(): void
    {
        putenv('SEED_ADMIN_EMAIL=sysadmin@example.test');
        putenv('SEED_ADMIN_PASSWORD=plaintext-from-env');

        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::count(), 'UserSeeder should create exactly 1 user');

        $admin = User::first();
        $this->assertSame('sysadmin@example.test', $admin->email);
        $this->assertTrue((bool) $admin->is_admin, 'Seeded user should be admin');
    }
}
