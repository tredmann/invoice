@extends('layouts.app')

@section('content')
    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.mail_receivers') }}</h1>
        <a href="{{ route('customerMailReceivers.create', ['customer' => $customer, 'tenant' => $tenant])}}" class="button is-danger is-radiusless">{{ __('customerMailReceivers.add') }}</a>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.mail_receivers') }}</h1>
        </div>

        @component('default.customers.components.customersMailReceiversTable', ['mailReceivers' => $mailReceivers, 'tenant' => $tenant])
        @endcomponent
    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'?>
