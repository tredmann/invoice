<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>{{ __('invoices.cancellation_invoice_for_invoice', ['invoice_no' => $invoice->cancelledInvoice?->invoice_no]) }}</title>
    <style>
        @page {
            size: A4;
            margin: 38mm 20mm 38mm 20mm;

            @top-left {
                content: element(pageHeader);
                width: 170mm;
                vertical-align: bottom;
                padding-bottom: 3mm;
                border-bottom: 1px solid #000;
            }

            @bottom-left {
                content: element(pageFooter);
                width: 170mm;
                vertical-align: top;
                padding-top: 3mm;
                border-top: 1px solid #000;
                font-size: 7.5pt;
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

        #pageHeader table {
            width: 170mm;
            border-collapse: collapse;
        }

        #pageHeader td {
            vertical-align: bottom;
            padding: 0;
        }

        #pageHeader td.logo-cell { width: 60%; }
        #pageHeader td.sender-cell { width: 40%; text-align: right; }

        #pageHeader img {
            max-height: 18mm;
            max-width: 60mm;
        }

        #pageHeader .caption {
            font-size: 8pt;
            margin-top: 1.5mm;
        }

        #pageFooter {
            position: running(pageFooter);
            font-size: 7.5pt;
        }

        #pageFooter table {
            width: 170mm;
            border-collapse: collapse;
        }

        #pageFooter td {
            vertical-align: top;
            padding: 0;
            line-height: 1.4;
        }

        #pageFooter td.left { width: 50%; }
        #pageFooter td.right { width: 50%; text-align: right; }

        #pageFooter::after {
            content: "{{ __('invoicePdf.page') }} " counter(page) " {{ __('invoicePdf.of') }} " counter(pages);
            display: block;
            text-align: center;
            margin-top: 4mm;
            font-size: 8pt;
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
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
        }

        .invoice-meta .label {
            color: #555;
        }

        .invoice-meta strong {
            font-weight: bold;
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
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 1mm 0;
        }

        .totals .grand td {
            border-top: 1px solid #000;
            font-weight: bold;
            padding-top: 2mm;
        }
    </style>
</head>
<body>

<div id="pageHeader">
    <table>
        <tr>
            <td class="logo-cell">
                @if(file_exists(public_path('images/png/500x261-balt.png')))
                    <img src="{{ public_path('images/png/500x261-balt.png') }}" alt="">
                @endif
                <div class="caption">{{ $generalInfo->name }} · {{ $generalInfo->street }} · {{ $generalInfo->postal }} {{ $generalInfo->city }}</div>
            </td>
            <td class="sender-cell">
                <div>{{ $generalInfo->name }}</div>
                <div>{{ $generalInfo->street }}</div>
                <div>{{ $generalInfo->postal }} {{ $generalInfo->city }}</div>
            </td>
        </tr>
    </table>
</div>

<div id="pageFooter">
    <table>
        <tr>
            <td class="left">
                <div>{{ __('attributes.registry_court') }}: {{ $legalInfo->registry_court }}</div>
                <div>{{ __('attributes.registry_no') }}: {{ $legalInfo->registry_no }}</div>
                <div>{{ __('attributes.company_owner') }}: {{ $legalInfo->company_owner }}</div>
            </td>
            <td class="right">
                <div>{{ __('attributes.tax_no') }}: {{ $legalInfo->tax_no }}</div>
                <div>{{ __('attributes.vat_no') }}: {{ $legalInfo->vat_no }}</div>
                <div>{{ __('attributes.swift_bic') }}: {{ $legalInfo->swift_bic }}</div>
                <div>{{ __('attributes.iban') }}: {{ $legalInfo->iban }}</div>
            </td>
        </tr>
    </table>
</div>

<x-pdf.customer-address :customer="$customer" />

<section class="invoice-meta">
    <div>
        <strong>
            {{ __('invoices.cancellation_invoice_for_invoice', ['invoice_no' => $invoice->cancelledInvoice?->invoice_no]) }}
        </strong>
    </div>
    <div style="text-align: right;">
        <div>{{ __('attributes.customer_no') }}: {{ $customer->customer_no }}</div>
        <div>{{ __('attributes.invoice_no') }}: {{ $invoice->invoice_no }}</div>
        <div>{{ __('translate.date') }}: {{ now()->tz('CET')->format('d.m.Y') }}</div>
        <div>{{ __('attributes.performed_when') }}: {{ $invoice->performed_when ?? sprintf('(%s)', trans('attributes.performed_when')) }}</div>
    </div>
</section>

<table class="line-items">
    <thead>
        <tr>
            <th>{{ __('lineItems.positionShort') }}</th>
            <th class="num">{{ __('attributes.quantity') }}</th>
            <th class="num">{{ __('attributes.price_each') }}</th>
            <th>{{ __('attributes.unit') }}</th>
            <th style="width: 50%;">{{ __('lineItems.details') }}</th>
            <th class="num">{{ __('attributes.without_tax') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->lineItems->sortBy('created_at') as $lineItem)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="num">{{ str_replace('.', ',', $lineItem->quantity) }}</td>
                <td class="num">-@money($lineItem->price_each, $lineItem->currency)</td>
                <td>{{ $lineItem->unit->label() }}</td>
                <td>
                    <p class="thick">{{ $lineItem->detail }}</p>
                    <p>{{ $lineItem->detail_plus }}</p>
                </td>
                <td class="num">-@money($lineItem->without_tax, $lineItem->currency)</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td>{{ __('attributes.total_without_tax') }}</td>
            <td class="num">-@money($invoice->total_without_tax, $invoice->currency)</td>
        </tr>
        {{-- NOTE: $lineItem is intentionally undefined here — preserves the pre-existing display quirk
             where the parenthesised per-tax without_tax value was always empty/null in the original template.
             This will be fixed in a separate pass. --}}
        @foreach($totalPerTax as $pair)
            <tr>
                <td>@tax($pair['percentage']) (-@money($lineItem->without_tax ?? 0, $invoice->currency))</td>
                <td class="num">-@money($pair['value'], $invoice->currency)</td>
            </tr>
        @endforeach
    </table>
</div>

<hr class="medium-thin">
<hr class="medium-thin">

<div class="totals">
    <table>
        <tr class="grand">
            <td>{{ __('attributes.total_with_tax') }}</td>
            <td class="num">-@money($invoice->total_with_tax, $invoice->currency)</td>
        </tr>
    </table>
</div>

</body>
</html>
