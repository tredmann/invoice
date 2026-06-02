<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Requests\LegalInfoRequest;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;

class LegalInfoController
{
    public function index(Tenant $tenant): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.legalInfos.index', [
            'legalInfo' => $tenant->currentLegalInfo,
            'tenant' => $tenant,
        ]);
    }

    public function create(Tenant $tenant): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.legalInfos.create', [
            'tenant' => $tenant,
        ]);
    }

    public function store(LegalInfoRequest $request, Tenant $tenant)
    {
        $legalInfo = LegalInfo::create($request->validated());
        $tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();

        return redirect(route('legalInfos.index', ['tenant' => $tenant]))->with(
            'success',
            'Rechtliche Angaben erfolgreich gespeichert!',
        );
    }

    public function edit(Tenant $tenant, LegalInfo $legalInfo): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.legalInfos.edit', [
            'legalInfo' => $legalInfo,
            'tenant' => $tenant,
        ]);
    }

    public function update(LegalInfoRequest $request, Tenant $tenant, LegalInfo $legalInfo)
    {
        $legalInfo->update($request->validated());

        return redirect(route('legalInfos.index', ['tenant' => $tenant]))->with(
            'success',
            'Rechtliche Angaben erfolgreich aktualisiert!',
        );
    }

    public function destroy(Tenant $tenant, LegalInfo $legalInfo)
    {
        $legalInfo->delete();

        return redirect(route('legalInfos.index', ['tenant' => $tenant]))->with(
            'deleted',
            'Rechtliche Angaben erfolgreich gelöscht!',
        );
    }
}
