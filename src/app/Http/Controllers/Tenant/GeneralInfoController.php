<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Requests\GeneralInfoRequest;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\Tenant;

class GeneralInfoController
{
    public function index(Tenant $tenant)
    {
        return view('default.generalInfos.index', [
            'generalInfo' => $tenant->currentGeneralInfo,
            'tenant' => $tenant,
        ]);
    }

    public function create(Tenant $tenant)
    {
        return view('default.generalInfos.create', [
            'tenant' => $tenant,
        ]);
    }

    public function store(GeneralInfoRequest $request, Tenant $tenant)
    {
        $generalInfo = GeneralInfo::create($request->validated());

        $tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        return redirect($tenant->route('generalInfos.index'))->with(
            'success',
            'Allgemeine Angaben erfolgreich gespeichert!',
        );
    }

    public function edit(Tenant $tenant, GeneralInfo $generalInfo)
    {
        return view('default.generalInfos.edit', [
            'generalInfo' => $generalInfo,
            'tenant' => $tenant,
        ]);
    }

    public function update(GeneralInfoRequest $request, Tenant $tenant, GeneralInfo $generalInfo)
    {
        $generalInfo->update($request->validated());

        return redirect($tenant->route('generalInfos.index'))->with(
            'success',
            'Allgemeine Angaben erfolgreich aktualisiert!',
        );
    }

    public function destroy(Tenant $tenant, GeneralInfo $generalInfo)
    {
        $generalInfo->delete();

        return redirect($tenant->route('generalInfos.index'))->with(
            'deleted',
            'Allgemeine Angaben erfolgreich gelöscht!',
        );
    }
}
