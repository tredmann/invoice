@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('invoices.invoices') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
{{--        <div id="content_head">--}}
{{--            <h1>{{ __('dashboard.paid_invoices') }}</h1>--}}
{{--        </div>--}}

        @component('default.invoices.components.invoicesList', ['invoices' => $invoices, 'tenant' => $tenant])
        @endcomponent
        {{ $invoices->links() }}
    </section>

@endsection


