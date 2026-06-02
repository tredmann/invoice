@extends('default.emails.layouts.mail')

@section('content')

    <p>
        {{$customerMailReceiver->getSalutation()}},<br>
        <br>
        anbei erhalten Sie Stornierungsrechnung für die Rechnung {{ $invoice->cancelledInvoice->invoice_no }}.
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
