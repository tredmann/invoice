@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('tenants.users') }}</h1>
        <a href="{{ route('tenants.invite-user-form', ['tenant' => $tenant])}}" class="button is-hidden-mobile is-danger is-radiusless">
            {{ __('tenants.invite_user') }}
        </a>
    </section>

    @component('default.tenants.components.tenantUsersTable',['tenantUsers' => $tenantUsers, 'tenant' => $tenant])
    @endcomponent

    {{ $tenantUsers->links() }}

@endsection

<?php $sidebarActive = true; $sidebar = 'default.tenants.components.tenantsSidebar'?>

