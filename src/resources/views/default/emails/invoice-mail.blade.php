 @extends('default.emails.layouts.mail')


@section('content')

    <img id="placeholder_image" src="{{ asset('images/png/500x261-balt.png') }}" style="display: block; width: 50%; margin: 0 auto; padding: 1rem; height: 75px; max-width: 150px;"/>

    <p>
        {{$customerMailReceiver->getSalutation()}},<br>
        <br>
        {{ __('invoices.invoice_mail.paragraph1') }}
        <br>
        <br>
        {{ __('invoices.invoice_mail.paragraph2', ['total_with_tax' =>money($invoice->total_with_tax, $invoice->currency), 'invoice_no' => $invoice->invoice_no, 'date_due' => $invoice->date_due->format('d.m.Y')]) }}
        <br>
        {{ __('attributes.invoice_no') }}: {{$invoice->invoice_no}}<br>
        {{ __('attributes.iban') }}: {{$tenant->currentLegalInfo->iban}}<br>
        {{ __('attributes.swift_bic') }}: {{$tenant->currentLegalInfo->swift_bic}}<br>
        <br>
        {{ __('invoices.invoice_mail.thanks') }}
        <br>
        <br>
        {{ __('invoices.invoice_mail.greetings') }},
        <br>{{$tenant->currentGeneralInfo->owner}}<br>
        <br>
        ---<br>
        {{$tenant->currentGeneralInfo->name}}<br>
        {{$tenant->currentGeneralInfo->street}}<br>
        {{$tenant->currentGeneralInfo->postal}} {{$tenant->currentGeneralInfo->city}} - {{$tenant->currentGeneralInfo->country}}<br>
        <br>
        {{ __('attributes.fax') }}: {{$tenant->currentGeneralInfo->fax}}<br>
        {{ __('attributes.email') }}: <a href="{{$tenant->currentGeneralInfo->email}}">{{$tenant->currentGeneralInfo->email}}</a><br>
        {{ __('attributes.homepage') }}: <a href="{{$tenant->currentGeneralInfo->homepage}}">{{$tenant->currentGeneralInfo->homepage}}</a><br>
        --<br>
    </p>

@endsection
