@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('invoices.show') }}</h1>
        @if ($invoice->status === $invoice::STATUS_DRAFT)
            <a href="{{route('lineItems.create', ['invoice' => $invoice, 'tenant' => $tenant])}}" class="button is-danger is-radiusless">
                {{ __('lineItems.add') }}
            </a>
        @endif
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('invoices.line_items') }}</h1>
        </div>

        @component('default.invoices.components.invoicesLineItemsTable', ['lineItems' => $invoice->lineItems, 'invoice' => $invoice, 'tenant' => $tenant])
        @endcomponent
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('invoices.invoice') }}</h1>
        </div>

        @component('default.invoices.components.invoicesShowTable', ['invoice' => $invoice, 'tenant' => $tenant])
        @endcomponent
    </section>
@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'; $customer=$invoice->customer?>
