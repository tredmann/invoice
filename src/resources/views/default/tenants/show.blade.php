@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('tenants.show') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1> {{ __('tenants.show') }} </h1>
            @component('default.tenants.components.tenantTriggerMenu', ['tenant' => $tenant])
            @endcomponent
        </div>

        @component('default.tenants.components.tenantsShowTable', ['tenant' => $tenant])
        @endcomponent

    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>
