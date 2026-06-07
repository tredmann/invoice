@extends('layouts.app')

@section('content')
    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('lineItems.add') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('lineItems.add') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{ route('lineItems.store', ['invoice' => $invoice, 'tenant' => $tenant]) }}">
        @csrf

        <!-- Quantity -->
            <div class="field">
                <label for="quantity" class="label">{{ __('attributes.quantity') }}</label>
                <div class="control">
                    <input id="quantity" name="quantity" type="number"
                           class="input"
                           onchange="toFloat(event)"
                           value="{{old('quantity')}}"
                           min="0"
                           step=".01"
                           max="999999999999"
                           required>
                </div>
            </div>

            <!-- Price each -->
            <div class="field">
                <label for="price_each" class="label">{{ __('attributes.price_each') }}</label>
                <div class="control">
                    <input id="price_each" name="price_each" type="number"
                           class="input"
                           onchange="toFloat(event)"
                           value="{{old('price_each')}}"
                           min="0"
                           step=".01"
                           max="9999999"
                           required>
                </div>
            </div>

            <!-- Currency -->
            @if(!$invoice->currency)
                <div class="field">
                    <label for="currency" class="label">{{ __('attributes.currency') }}</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="currency" name="currency" required>
                                @foreach(\App\Models\Money::CURRENCIES as $currency)
                                    <option value="{{$currency}}" @if(old('currency') === $currency) selected @endif>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @else
                <div class="field">
                    <label for="currency" class="label">{{ __('attributes.currency') }}</label>
                    <div class="control">
                        <input id="currency" name="currency" type="text"
                               class="input"
                               value="{{$invoice->currency}}"
                               disabled
                               required>
                    </div>
                </div>

                <input type="hidden" name="currency" value="{{$invoice->currency}}">
        @endif

        <!-- Tax rate -->
            <div class="field">
                <label for="tax_rate" class="label">{{ __('attributes.tax_rate') }}</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select id="tax_rate" name="tax_rate" required>
                            @foreach(\App\Models\Money::DE_TAX_RATES as $tax_rate)
                                <option value="{{$tax_rate}}" @if(old('tax_rate') === $tax_rate) selected @endif>@tax($tax_rate)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Unit -->
            <div class="field">
                <label for="unit" class="label">{{ __('attributes.unit') }}</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select id="unit" name="unit" required>
                            @foreach(\App\Enums\UnitCode::cases() as $code)
                                <option value="{{ $code->value }}" {{ old('unit', \App\Enums\UnitCode::Piece->value) === $code->value ? 'selected' : '' }}>
                                    {{ $code->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detail -->
            <div class="field">
                <label for="detail" class="label">{{ __('attributes.detail') }}</label>
                <div class="control">
                    <input id="detail" name="detail" type="text"
                           class="input"
                           value="{{old('detail')}}"
                           required>
                </div>
            </div>

            <!-- Detail Plus -->
            <div class="field">
                <label for="detail_plus" class="label">{{ __('attributes.detail_plus') }}</label>
                <div class="control">
                        <textarea id="detail_plus" name="detail_plus"
                                  class="textarea"
                                  rows="4"
                                  placeholder="{{ __('translate.optional') }}"
                                  >{{old('detail_plus')}}</textarea>
                </div>

                <section id="button_section" class="mt-6">
                    <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                        <!-- Cancel -->
                        <a href="{{ $tenant->route('invoices.show', ['invoice' => $invoice]) }}"
                           class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                        <!-- Continue -->
                        <button name="submit"
                                value="continue"
                                type="submit"
                                class="button is-danger is-radiusless">{{ __('lineItems.add_more') }}</button>

                        <!-- Done -->
                        <button name="submit"
                                value="done"
                                type="submit"
                                class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                    </div>
                </section>
            </div>
        </form>
    </section>
@endsection

@section('scripts@Header')
    <script type="text/javascript">
        function toFloat(event) {
            const x = event.target;
            x.value = parseFloat(x.value).toFixed(2);
        }
    </script>
@endsection

<?php $sidebarActive = true; $sidebar = 'default.customers.components.customersSidebar'; $customer=$invoice->customer?>
