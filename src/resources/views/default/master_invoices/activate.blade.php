@extends('layouts.app')

@section('content')
    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('masterInvoices.activate') }}</h1>
    </section>


    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('masterInvoices.activate_sentence') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('masterInvoices.active', ['masterInvoice' => $masterInvoice])}}">
        @csrf
        @method('PATCH')

        <!-- Days till due-->
            <div class="field">
                <label for="days_till_due" class="label">{{ __('attributes.days_till_due') }}</label>
                @foreach(\App\Models\MasterInvoice::DAYS_TILL_DUE as $days)
                    <input class="is-checkradio is-danger" id="days_till_due_{{$days}}" type="radio" name="days_till_due" value="{{$days}}" required>
                    <label for="days_till_due_{{$days}}">{{ __('masterInvoices.days_till_due.' . $days) }}</label>
                @endforeach
            </div>

            <!-- Billing frequency-->
            <div class="field">
                <label for="days_till_due" class="label">{{ __('attributes.billing_frequency') }}</label>
                @foreach(\App\Models\MasterInvoice::BILLING_FREQUENCIES as $frequency)
                    <input class="is-checkradio is-danger" id="billing_frequency_{{$frequency}}" type="radio" name="billing_frequency" value="{{$frequency}}" required>
                    <label for="billing_frequency_{{$frequency}}">{{ __('masterInvoices.billing_frequency.' . $frequency) }}</label>
                @endforeach
            </div>

            <!-- Next print -->
            <div class="field">
                <label for="next_print" class="label">{{ __('attributes.next_print') }}</label>
                <div class="control">
                    <input id="next_print" name="next_print" type="date"
                           class="input"
                           value="{{old('next_print')}}"
                           required>
                </div>
            </div>

            <!-- Total -->
            <div class="field">
                <label for="total" class="label">{{ __('translate.total') }}</label>
                <div class="control">
                    <input id="total" name="total" type="text" disabled
                           class="input"
                           value="{{ money($masterInvoice->total_without_tax, $masterInvoice->currency) }}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('customers.invoices', ['customer' => $masterInvoice->customer])}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Conclude -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('masterInvoices.activate') }}
                    </button>
                </div>
            </section>
        </form>
    </section>
@endsection


