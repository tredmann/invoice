@extends('layouts.app')

@section('content')
    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.show') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1> {{ __('customers.show') }} </h1>
            @component('default.customers.components.customersShowTriggerMenu', ['customer' => $customer, 'tenant' => $customer->tenant])
            @endcomponent
        </div>

        @component('default.customers.components.customersShowTable', ['customer' => $customer, 'tenant' => $customer->tenant])
        @endcomponent
    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'?>
