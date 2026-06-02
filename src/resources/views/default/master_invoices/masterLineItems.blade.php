@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('masterLineItems.master_line_items') }}</h1>
        <a href="{{$tenant->route('masterLineItems.create', ['masterInvoice' => $masterInvoice])}}" class="button is-danger is-radiusless">
            {{ __('masterLineItems.add') }}
        </a>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('masterLineItems.master_line_items') }}</h1>
        </div>

        @component('default.master_invoices.components.masterInvoicesMasterLineItemsTable', ['masterLineItems' => $masterLineItems, 'masterInvoice' => $masterInvoice, 'tenant' => $tenant])
        @endcomponent

    </section>
@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'; $customer=$masterInvoice->customer?>
