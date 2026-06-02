<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Requests\AddUserRequest;
use App\Http\Requests\TenantRequest;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Auth;

class TenantController
{
    public function __construct(private TenantService $tenantService)
    {
    }

    public function index()
    {
        return view('default.tenants.index', [
            'tenants' => Auth::user()
                ->tenants()
                ->paginate(15),
        ]);
    }

    public function create()
    {
        return view('default.tenants.create');
    }

    public function store(TenantRequest $request)
    {
        $tenant = $this->tenantService->store($request->validated());

        return redirect(route('tenants.users', ['tenant' => $tenant]))->with('success', 'Tenant erfolgreich gespeichert!');
    }

    public function edit(Tenant $tenant)
    {
        return view('default.tenants.edit', [
            'tenant' => $tenant,
        ]);
    }

    public function show(Tenant $tenant)
    {
        return view('default.tenants.show', ['tenant' => $tenant]);
    }

    public function users(Tenant $tenant)
    {
        return view('default.tenants.users', [
            'tenantUsers' => $tenant
                ->users()
                ->paginate(15),
            'tenant' => $tenant,
        ]);
    }

    public function inviteUserForm(Tenant $tenant)
    {
        return view('default.tenants.inviteUser', [
            'tenant' => $tenant,
        ]);
    }

    public function inviteUser(AddUserRequest $request, Tenant $tenant)
    {
        $this->tenantService->addUser($request->validated(), $tenant);

        return redirect(route('tenants.users', ['tenant' => $tenant]))->with(
            'success',
            'Nutzer erfolgreich zu Tenant hinzugefügt!',
        );
    }

    public function removeUser(Tenant $tenant, User $user)
    {
        $tenant
            ->users()
            ->detach($user);

        return redirect($tenant->route('tenants.users'))->with(
            'success',
            'Nutzer erfolgreich aus Tenant entfernt!',
        );
    }

    public function update(TenantRequest $request, Tenant $tenant)
    {
        $this->tenantService->update($request->validated(), $tenant);

        return redirect($tenant->route('tenants.show'))->with('success', 'Tenant erfolgreich aktualisiert!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()
            ->route('tenants.index')
            ->with('deleted', 'Tenant erfolgreich gelöscht!');
    }
}
