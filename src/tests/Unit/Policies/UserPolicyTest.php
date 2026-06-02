<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use DatabaseTransactions;

    public function testIsAdminReturnsTrueForAdminUser(): void
    {
        $admin = User::factory()->isAdmin()->create();

        self::assertTrue((new UserPolicy())->isAdmin($admin));
    }

    public function testIsAdminReturnsFalseForNonAdminUser(): void
    {
        $user = User::factory()->create();

        self::assertFalse((new UserPolicy())->isAdmin($user));
    }
}
