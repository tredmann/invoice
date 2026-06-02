@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.invoices') }}</h1>
        <form method="post" action="{{ route('invoices.store', ['customer' => $customer, 'tenant' => $tenant])}}">
            @csrf
            <button type="submit" class="button is-danger is-radiusless">
                {{ __('invoices.add') }}
            </button>
        </form>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.invoices') }}</h1>
        </div>

        @component('default.customers.components.customersInvoicesTable', ['invoices' => $invoices, 'tenant' => $tenant, 'customer' => $customer])
        @endcomponent
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1>{{ __('customers.drafts') }}</h1>
        </div>
        @component('default.customers.components.customersDraftInvoicesTable', ['customer' => $customer, 'invoices' => $invoices, 'drafts' => $drafts, 'tenant' => $tenant])
        @endcomponent
    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'?>
