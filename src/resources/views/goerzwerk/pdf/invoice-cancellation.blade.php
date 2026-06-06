<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Stornierung {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            size: A4;
            margin: 45mm 20mm 42mm 20mm;

            @top-left {
                content: element(pageHeader);
                width: 170mm;
                vertical-align: bottom;
                padding-bottom: 3mm;
            }

            @bottom-left {
                content: element(pageFooter);
                width: 145mm;
                vertical-align: top;
                padding-top: 3mm;
                font-size: 7.5pt;
            }

            @bottom-right {
                content: "{{ __('invoicePdf.page') }} " counter(page) " {{ __('invoicePdf.of') }} " counter(pages);
                width: 25mm;
                vertical-align: top;
                padding-top: 3mm;
                font-size: 8pt;
                text-align: right;
                white-space: nowrap;
            }
        }

        body {
            font-family: "DejaVu Sans", "Noto Sans", sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
        }

        #pageHeader {
            position: running(pageHeader);
            font-size: 9pt;
        }

        #pageHeader .logo-row {
            text-align: right;
        }

        #pageHeader img {
            width: 6cm;
            height: auto;
        }

        #pageHeader .header-line {
            text-transform: uppercase;
            margin-top: 2mm;
            font-size: 9pt;
        }

        #pageFooter {
            position: running(pageFooter);
            font-size: 7.5pt;
        }

        #pageFooter table {
            width: 145mm;
            border-collapse: collapse;
            table-layout: fixed;
        }

        #pageFooter td.footer-cell {
            width: 25%;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2mm;
            text-transform: uppercase;
            vertical-align: top;
        }

        #pageFooter td.footer-cell div {
            margin: 0;
            line-height: 1.4;
        }

        .customer-address {
            margin: 6mm 0 4mm;
        }

        .customer-address p {
            margin: 1mm 0;
        }

        .invoice-meta {
            margin: 4mm 0 6mm;
            padding: 2mm 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
        }

        .invoice-meta strong {
            font-weight: bold;
        }

        .invoice-meta .meta-right {
            text-align: right;
        }

        .invoice-meta .meta-right div {
            margin: 1mm 0;
        }

        table.line-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
        }

        table.line-items thead th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 2mm 1mm;
            font-size: 10pt;
        }

        table.line-items tbody td {
            border-bottom: 1px solid #ccc;
            padding: 2mm 1mm;
            vertical-align: top;
            font-size: 9pt;
        }

        table.line-items tbody tr {
            break-inside: avoid;
        }

        table.line-items td p {
            margin: 1px 0;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .thick {
            font-weight: bold;
        }

        .totals {
            margin-top: 4mm;
            break-inside: avoid;
        }

        .totals table {
            width: 60%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 1mm 2mm;
        }

        hr.medium-thin {
            border: none;
            border-top: 1px solid #000;
            margin: 2mm 0 2mm auto;
            width: 60%;
        }
    </style>
</head>
<body>

<div id="pageHeader">
    <div class="logo-row"><img src="data:image/png;base64,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" alt=""></div>
    <div class="header-line"><strong>{{$generalInfo->name}}</strong>&nbsp;&nbsp;{{$generalInfo->street}}&nbsp;&nbsp;{{$generalInfo->postal}} {{$generalInfo->city}}</div>
</div>

<div id="pageFooter">
    <table>
        <tr>
            <td class="footer-cell">
                <div><strong>{{$generalInfo->name}}</strong></div>
                <div>{{$generalInfo->street}}</div>
                <div>{{$generalInfo->postal}} {{$generalInfo->city}}</div>
            </td>
            <td class="footer-cell">
                <div><strong>Kontakt</strong></div>
                <div>Tel {{$generalInfo->telephone}}</div>
                <div>Fax {{$generalInfo->fax}}</div>
                <div>{{$generalInfo->email}}</div>
                <div>{{$generalInfo->homepage}}</div>
            </td>
            <td class="footer-cell">
                <div><strong>Geschäftsführende<br/>Gesellschafter</strong></div>
                <div>Silvio Schobinger</div>
                <div>Mario Schobinger</div>
                <div>AG Charlottenburg</div>
                <div>HRA 50203 B</div>
            </td>
            <td class="footer-cell">
                <div><strong>{{ $legalInfo->bank_institute}}</strong></div>
                <div>IBAN: {{$legalInfo->iban}}</div>
                <div>BIC: {{$legalInfo->swift_bic}}</div>
                <div>&nbsp;</div>
                <div>St.-Nr. {{$legalInfo->tax_no}}</div>
                <div>Ust-ID {{$legalInfo->vat_no}}</div>
            </td>
        </tr>
    </table>
</div>

<x-pdf.customer-address :customer="$customer" />

<section class="invoice-meta">
    <div>
        <p class="thick">Stornierung für Rechnungs-Nr. {{ $invoice->cancelledInvoice?->invoice_no }}</p>
    </div>
    <div class="meta-right">
        <div>{{ __('translate.date') }}: {{now()->tz('CET')->format('d.m.Y')}}</div>
        <div>{{ __('attributes.customer_no') }}: {{$customer->customer_no}}</div>
        <div>Rechnungs-Nr.:
            {{$invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 4))}}
        </div>
    </div>
</section>

<table class="line-items">
    <thead>
        <tr>
            <th style="width: 1.5cm;">{{ __('lineItems.positionShort') }}</th>
            <th style="width: 50%;">{{ __('lineItems.details') }}</th>
            <th class="num" style="width: 2cm;">{{ __('attributes.without_tax') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->lineItems->sortBy('created_at') as $lineItem)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>
                    <p class="thick">{{$lineItem->detail}}</p>
                    <p>{!! nl2br($lineItem->detail_plus) !!}</p>
                </td>
                <td class="num">-@money($lineItem->without_tax, $lineItem->currency)</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td class="thick">{{ __('attributes.total_without_tax') }}</td>
            <td class="num thick">-@money($invoice->total_without_tax, $invoice->currency)</td>
        </tr>
        @foreach($invoice->getTaxDistribution() as $taxRate => $tax)
            <tr>
                <td>{{$taxRate}}% (@money($tax['without_tax'], $invoice->currency))</td>
                <td class="num">-@money($tax['tax'], $invoice->currency)</td>
            </tr>
        @endforeach
    </table>
</div>

<hr class="medium-thin">

<div class="totals">
    <table>
        <tr>
            <td class="thick">{{ __('attributes.total_with_tax') }}</td>
            <td class="num thick">-@money($invoice->total_with_tax, $invoice->currency)</td>
        </tr>
    </table>
</div>

</body>
</html>
