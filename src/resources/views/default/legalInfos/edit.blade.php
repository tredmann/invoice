@extends('layouts.app')

@section('content')

    <section id="header_section">
        <h1 class="title is-size-5 is-uppercase">{{ __('legalInfos.edit') }}</h1>
    </section>

    <section id="content_section" class="mt-5 box pb-1 p-0">
        <div id="content_head">
            <h1>{{ __('legalInfos.edit') }}</h1>
        </div>

        <form id="content_form" class="p-4" method="POST" action="{{$tenant->route('legalInfos.update', ['legalInfo' => $legalInfo])}}">
        @csrf
        @method('PATCH')

        <!-- Registry Court -->
            <div class="field">
                <label for="registry_court" class="label">{{ __('attributes.registry_court') }}</label>
                <div class="control">
                    <input id="registry_court" name="registry_court" type="text"
                           class="input"
                           value="{{old('registry_court') ?? $legalInfo->registry_court}}"
                           required>
                </div>
            </div>

            <!-- Registry No -->
            <div class="field">
                <label for="registry_no" class="label">{{ __('attributes.registry_no') }}</label>
                <div class="control">
                    <input id="registry_no" name="registry_no" type="text"
                           class="input"
                           value="{{old('registry_no') ?? $legalInfo->registry_no}}"
                           required>
                </div>
            </div>

            <!-- Company Owner -->
            <div class="field">
                <label for="company_owner" class="label">{{ __('attributes.company_owner') }}</label>
                <div class="control">
                    <input id="company_owner" name="company_owner" type="text"
                           class="input"
                           value="{{old('company_owner') ?? $legalInfo->company_owner}}"
                           required>
                </div>
            </div>

            <!-- Tax No -->
            <div class="field">
                <label for="tax_no" class="label">{{ __('attributes.tax_no') }}</label>
                <div class="control">
                    <input id="tax_no" name="tax_no" type="text"
                           class="input"
                           value="{{old('tax_no') ?? $legalInfo->tax_no}}"
                           required>
                </div>
            </div>

            <!-- Vat No -->
            <div class="field">
                <label for="vat_no" class="label">{{ __('attributes.vat_no') }}</label>
                <div class="control">
                    <input id="vat_no" name="vat_no" type="text"
                           class="input"
                           value="{{old('vat_no') ?? $legalInfo->vat_no}}"
                           required>
                </div>
            </div>

            <!-- SWIFT/BIC -->
            <div class="field">
                <label for="swift_bic" class="label">{{ __('attributes.swift_bic') }}</label>
                <div class="control">
                    <input id="swift_bic" name="swift_bic" type="text"
                           class="input"
                           value="{{old('swift_bic') ?? $legalInfo->swift_bic}}"
                           required>
                </div>
            </div>

            <!-- IBAN -->
            <div class="field">
                <label for="iban" class="label">{{ __('attributes.iban') }}</label>
                <div class="control">
                    <input id="iban" name="iban" type="text"
                           class="input"
                           value="{{old('iban') ?? $legalInfo->iban}}"
                           required>
                </div>
            </div>

            <!-- Bank Institute -->
            <div class="field">
                <label for="bank_institute" class="label">{{ __('attributes.bank_institute') }}</label>
                <div class="control">
                    <input id="bank_institute" name="bank_institute" type="text"
                           class="input"
                           value="{{old('bank_institute') ?? $legalInfo->bank_institute}}"
                           required>
                </div>
            </div>

            <section id="button_section" class="mt-6">
                <div class="mt-5 is-flex is-justify-content-space-between is-align-items-center">
                    <!-- Cancel -->
                    <a href="{{$tenant->route('legalInfos.index')}}"
                       class="button is-outlined is-radiusless">{{ __('translate.cancel') }}</a>

                    <!-- Save -->
                    <button type="submit"
                            class="button is-danger is-radiusless">{{ __('translate.save') }}</button>
                </div>
            </section>
        </form>
    </section>

@endsection
