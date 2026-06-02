@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('invoices.conclude') }}</h1>
    </section>


    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('invoices.conclude_sentence') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{route('invoices.open', ['invoice' => $invoice, 'tenant' => $tenant])}}">
            @method('PATCH')
            @csrf

        <!-- Days till due-->
            <div class="field">
                <label for="days_till_due" class="label">{{ __('attributes.days_till_due') }}</label>
                @foreach(\App\Models\Invoice::DAYS_TILL_DUE as $days)
                    <input class="is-checkradio is-danger" id="days_till_due_{{$days}}" type="radio" name="days_till_due" value="{{$days}}" required>
                    <label for="days_till_due_{{$days}}">{{ __('invoices.days_till_due.' . $days) }}</label>
                @endforeach
            </div>

            <!-- Performed when -->
            <div class="field">
                <label for="performed_when" class="label">Leistungszeitraum</label>
                <div class="control">
                    <input id="performed_when" name="performed_when" type="text"
                           class="input "
                           placeholder="{{__('translate.eg')}} {{ now()->monthName }} {{ now()->year }}"
                           value="{{old('performed_when')}}"
                           required>
                </div>
            </div>

            <!-- Total -->
            <div class="field">
                <label for="total" class="label">{{ __('translate.total') }}</label>
                <div class="control">
                    <input id="total" name="total" type="text" disabled
                           class="input"
                           value="{{ money($invoice->total_without_tax, $invoice->currency) }}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{ route('customers.invoices', ['customer' => $invoice->customer, 'tenant' => $tenant]) }}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Conclude -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('invoices.conclude') }}
                    </button>
                </div>
            </section>
        </form>
    </section>
@endsection


