@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.customers') }}</h1>
        <a href="{{ route('customers.create', ['tenant' => $tenant])}}" class="button is-danger is-radiusless">
            {{ __('customers.add') }}
        </a>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.customers') }}</h1>
        </div>

        @component('default.customers.components.customersIndexTable', ['customers' => $customers, 'tenant' => $tenant])
        @endcomponent
    </section>
@endsection
