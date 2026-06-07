@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('customers.edit') }}</h1>
    </section>


    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('customers.edit') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{route('customers.update', ['customer' => $customer, 'tenant' => $tenant])}}">
        @csrf
        @method('PATCH')

        @if(\App\Models\Setting::isEnabled($tenant, \App\Enums\Settings::EDIT_CUSTOMER_ID))

                <div class="field">
                    <label for="customer_no" class="label">{{ __('customers.customer_no') }}</label>
                    <div class="control">
                        <input id="customer_no" name="customer_no" type="text"
                               class="input"
                               value="{{old('customer_no') ?? $customer->customer_no}}"
                               required>
                    </div>
                </div>

            @else
                <div class="field">
                    <label for="customer_no" class="label">{{ __('customers.customer_no') }}</label>
                    <div class="control">
                        <input id="customer_no" name="customer_no" type="text"
                               class="input "
                               value="{{old('customer_no') ?? $customer->customer_no}}"
                               readonly
                               disabled
                               required>
                    </div>
                </div>

        @endif

        <!-- Company -->
            <div class="field">
                <label for="company" class="label">{{ __('customers.company') }}</label>
                <div class="control">
                    <input id="company" name="company" type="text"
                           class="input"
                           value="{{old('company') ?? $customer->company}}"
                           required>
                </div>
            </div>

            <!-- Name -->
            <div class="field">
                <label for="name" class="label">{{ __('customers.name') }}</label>
                <div class="control">
                    <input id="name" name="name" type="text"
                           class="input"
                           value="{{old('name') ?? $customer->name}}"
                           placeholder="{{ __('translate.optional') }}">
                </div>
            </div>

            <!-- Street -->
            <div class="field">
                <label for="street" class="label">{{ __('customers.street') }}</label>
                <div class="control">
                    <input id="street" name="street" type="text"
                           class="input"
                           value="{{old('street') ?? $customer->street}}"
                           required>
                </div>
            </div>

            <!-- Postal -->
            <div class="field">
                <label for="postal" class="label">{{ __('customers.postal') }}</label>
                <div class="control">
                    <input id="postal" name="postal" type="text" maxlength="5"
                           class="input"
                           value="{{old('postal') ?? $customer->postal}}"
                           required>
                </div>
            </div>

            <!-- City -->
            <div class="field">
                <label for="city" class="label">{{ __('customers.city') }}</label>
                <div class="control">
                    <input id="city" name="city" type="text"
                           class="input"
                           value="{{old('city') ?? $customer->city}}"
                           required>
                </div>
            </div>

            <!-- Country -->
            <div class="field">
                <label for="country" class="label">{{ __('customers.country') }}</label>
                <div class="control">
                    <div class="select">
                        <select id="country" name="country" required>
                            <option value="DE" {{ (old('country') ?? $customer->country) === 'DE' ? 'selected' : '' }}>Deutschland</option>
                        </select>
                    </div>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{ url()->previous() }}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Create -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>

@endsection
