@extends('layouts.app')

@section('content')
    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('generalInfos.general_infos') }}</h1>

        @can('isFirst', \App\Models\Tenant\GeneralInfo::class)
            <a href="{{$tenant->route('generalInfos.create')}}" class="button is-danger is-radiusless">
                {{ __('generalInfos.add') }}
            </a>
        @endif
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1>{{ __('generalInfos.general_infos') }}</h1>
            @if($generalInfo)
                @component('default.generalInfos.components.generalInfosTriggerMenu', ['generalInfo' => $generalInfo, 'tenant' => $tenant])
                @endcomponent
            @endif
        </div>

        @component('default.generalInfos.components.generalInfosShowTable', ['generalInfo' => $generalInfo, 'tenant' => $tenant])
        @endcomponent

    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>

