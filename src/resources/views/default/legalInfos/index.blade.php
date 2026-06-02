@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('legalInfos.legal_infos') }}</h1>

        @can('isFirst', \App\Models\Tenant\LegalInfo::class)
            <a href="{{$tenant->route('legalInfos.create')}}" class="button is-danger is-radiusless">
                {{ __('legalInfos.add') }}
            </a>
        @endif
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1>{{ __('legalInfos.legal_infos') }}</h1>
            @if($legalInfo)
                @component('default.legalInfos.components.legalInfosTriggerMenu', ['legalInfo' => $legalInfo, 'tenant' => $tenant])
                @endcomponent
            @endif
        </div>

        @component('default.legalInfos.components.legalInfosShowTable', ['legalInfo' => $legalInfo, 'tenant' => $tenant])
        @endcomponent

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>
