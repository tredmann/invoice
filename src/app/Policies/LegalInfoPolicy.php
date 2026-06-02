<?php

namespace App\Policies;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalInfoPolicy
{
    use HandlesAuthorization;

    public function isFirst(User $user): bool
    {
        return app(Tenant::class)->currentLegalInfo === null;
    }

    public function isFirstWithTenant(User $user, Tenant $tenant): bool
    {
        return $tenant->currentLegalInfo === null;
    }
}
