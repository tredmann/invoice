<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralInfoPolicy
{
    use HandlesAuthorization;

    public function isFirst(User $user): bool
    {
        return app(Tenant::class)->currentGeneralInfo === null;
    }
}
