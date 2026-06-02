@extends('layouts.app')

@section('content')

    <section id="header_section" class="is-flex is-justify-content-space-between">
        <h1 class="title is-size-5 is-uppercase">{{ __('tenants.tenants') }}</h1>
        <a href="{{route('tenants.create')}}" class="button is-hidden-mobile is-danger is-radiusless">
            {{ __('tenants.add') }}
        </a>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.customers') }}</h1>
        </div>

        @component('default.tenants.components.tenantsTable',['tenants' => $tenants])
        @endcomponent
    </section>

@endsection
