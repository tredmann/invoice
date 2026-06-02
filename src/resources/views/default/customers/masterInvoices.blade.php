@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.master_invoices') }}</h1>
        <form method="post" action="{{ route('masterInvoices.store', ['customer' => $customer, 'tenant' => $tenant])}}">
            @csrf
            <button type="submit" class="button is-danger is-radiusless">
                {{ __('masterInvoices.add') }}
            </button>
        </form>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.master_invoices') }}</h1>
        </div>

        @component('default.customers.components.customersMasterInvoicesTable', ['masterInvoices' => $masterInvoices, 'tenant' => $tenant])
        @endcomponent
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head--with-menu">
            <h1>{{ __('customers.drafts') }}</h1>
        </div>

        @component('default.customers.components.customersDraftMasterInvoicesTable', ['customer' => $customer, 'masterInvoices' => $masterInvoices, 'drafts' => $drafts])
        @endcomponent
    </section>

@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'?>
