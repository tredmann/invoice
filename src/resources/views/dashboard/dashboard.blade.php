@extends('layouts.app')

@section('content')
    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('dashboard.dashboard') }}</h1>
    </section>

    {{--
    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('dashboard.paid_invoices') }}</h1>
        </div>

        @component('dashboard.components.dashboardPaidInvoicesTable', ['paidInvoices' => $paidInvoices])
        @endcomponent
    </section>
    --}}

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('dashboard.open_invoices') }}</h1>
        </div>

        @component('dashboard.components.dashboardOpenInvoicesTable', ['openInvoices' => $openInvoices, 'tenant' => $tenant])
        @endcomponent
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('dashboard.overdue_invoices') }}</h1>
        </div>

        @component('dashboard.components.dashboardOverdueInvoicesTable', ['overdueInvoices' => $overdueInvoices, 'tenant' => $tenant])
        @endcomponent
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('dashboard.master_invoices_sent_within_30_days') }}</h1>
        </div>

        @component('dashboard.components.dashboardMasterInvoices30Table', ['masterInvoicesSentWithin30Days' => $masterInvoicesSentWithin30Days, 'tenant' => $tenant])
        @endcomponent
    </section>
@endsection
