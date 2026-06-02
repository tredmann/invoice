<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function isOwner(User $user): bool
    {
        return app(Tenant::class)->owner_id == $user->id;
    }

    public function isOwnerViewGate(User $user, Tenant $tenant): bool
    {
        return $tenant->owner->id === $user->id;
    }
}
