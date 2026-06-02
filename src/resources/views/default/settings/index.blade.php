@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('settings.settings') }}</h1>
        <a href="{{$tenant->route('settings.testEmailSettings')}}" class="button is-danger is-radiusless" style="margin-left: auto; margin-right: 10px">
            {{ __('settings.test_email_settings') }}
        </a>
        <a href="{{$tenant->route('settings.create')}}" class="button is-danger is-radiusless">
            {{ __('settings.add') }}
        </a>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('settings.settings') }}</h1>
        </div>

        @component('default.settings.components.settingsTable', ['settings' => $settings, 'tenant' => $tenant])
        @endcomponent

    </section>

    {{ $settings->links() }}

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>
