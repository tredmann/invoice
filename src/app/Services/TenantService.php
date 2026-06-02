<?php

namespace App\Services;

use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TenantService
{
    public function store(array $input)
    {
        $authID = Auth::id();

        $input['slug'] = Str::slug($input['name']);
        $input['owner_id'] = $authID;

        $tenant = Tenant::create($input);
        $tenant->users()->attach($authID);

        return $tenant;
    }

    public function update(array $input, Tenant $tenant): void
    {
        $input['slug'] = Str::slug($input['name']);

        $tenant->update($input);
    }

    public function addUser(array $input, Tenant $tenant): void
    {
        $user = User::where('email', $input['email'])->first();

        $tenant->users()->attach($user);
    }
}
