<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@example.test');
        $password = env('SEED_ADMIN_PASSWORD') ?: Str::random(16);

        $user = new User([
            'name' => 'Platform Admin',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'remember_token' => Str::random(10),
        ]);
        $user->is_admin = true;
        $user->save();

        if (! env('SEED_ADMIN_PASSWORD')) {
            $this->command->warn("Generated admin password for {$email}: {$password}");
        }
    }
}
