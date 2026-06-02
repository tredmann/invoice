@extends('default.emails.layouts.mail')

@section('content')

    <p>
        {{$customerMailReceiver->getSalutation()}},<br>
        <br>
        anbei erhalten Sie die Abrechnung Ihres Stromverbrauchs. Nähere Informationen entnehmen Sie
        bitte der angehängten PDF-Datei.
        <br>
        <br>
        {!!  __('invoices.invoice_mail.paragraph2', ['total_with_tax' =>money($invoice->total_with_tax, $invoice->currency), 'invoice_no' => $invoice->invoice_no, 'date_due' => $invoice->date_due->format('d.m.Y')]) !!}
        <br><br>
        <strong>Bitte beachten Sie, dass sich unsere Bankverbindung geändert hat.</strong>
        <br><br>
        {{ __('attributes.invoice_no') }}: {{$invoice->invoice_no}}<br>
        {{ __('attributes.iban') }}: {{$tenant->currentLegalInfo->iban}}<br>
        {{ __('attributes.swift_bic') }}: {{$tenant->currentLegalInfo->swift_bic}}<br>
        <br>
        {{ __('invoices.invoice_mail.thanks') }}
        <br>
        <br>
        Liebe Grüße,<br>
        Goerzwerk Verwaltung
        <br>
        ---<br>
        <strong>GOERZWERK</strong><br>
        Goerzallee 299 · 14167 Berlin<br>
        T +49 30 290 27 68 11<br>
        <a href='mailto:stromrechnung@goerzwerk.de'>stromrechnung@goerzwerk.de</a><br>
        <a href='https://www.goerzwerk.de'>www.goerzwerk.de</a><br>

    </p>

@endsection
