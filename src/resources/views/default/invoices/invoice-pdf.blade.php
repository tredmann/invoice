<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>{{ __('invoices.invoice') }} {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            size: A4;
            margin: 25mm 20mm 35mm 20mm;

            @top-center {
                content: element(pageHeader);
                vertical-align: bottom;
                padding-bottom: 4mm;
                border-bottom: 1px solid #000;
            }

            @bottom-left {
                content: element(pageFooter);
                vertical-align: top;
                padding-top: 4mm;
                border-top: 1px solid #000;
                font-size: 8pt;
            }

            @bottom-right {
                content: "{{ __('invoicePdf.page') }} " counter(page) " {{ __('invoicePdf.of') }} " counter(pages);
                font-size: 8pt;
                vertical-align: bottom;
                padding-bottom: 2mm;
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
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 9pt;
        }

        #pageHeader img {
            max-height: 22mm;
            max-width: 60mm;
        }

        #pageFooter {
            position: running(pageFooter);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8mm;
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
            width: 60%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 1mm 2mm;
        }

        .totals .grand td {
            border-top: 1px solid #000;
            font-weight: bold;
        }

        .payment {
            margin-top: 8mm;
            break-inside: avoid;
        }

        .payment p {
            margin: 2mm 0;
        }
    </style>
</head>
<body>

<div id="pageHeader">
    <div>
        @if(file_exists(public_path('images/png/500x261-balt.png')))
            <img src="{{ public_path('images/png/500x261-balt.png') }}" alt="">
        @endif
        <div>{{ $generalInfo->name }} · {{ $generalInfo->street }} · {{ $generalInfo->postal }} {{ $generalInfo->city }}</div>
    </div>
    <div style="text-align: right;">
        <div>{{ $generalInfo->name }}</div>
        <div>{{ $generalInfo->street }}</div>
        <div>{{ $generalInfo->postal }} {{ $generalInfo->city }}</div>
    </div>
</div>

<div id="pageFooter">
    <div>
        <div>{{ __('attributes.registry_court') }}: {{ $legalInfo->registry_court }}</div>
        <div>{{ __('attributes.registry_no') }}: {{ $legalInfo->registry_no }}</div>
        <div>{{ __('attributes.company_owner') }}: {{ $legalInfo->company_owner }}</div>
    </div>
    <div style="text-align: right;">
        <div>{{ __('attributes.tax_no') }}: {{ $legalInfo->tax_no }}</div>
        <div>{{ __('attributes.vat_no') }}: {{ $legalInfo->vat_no }}</div>
        <div>{{ __('attributes.swift_bic') }}: {{ $legalInfo->swift_bic }}</div>
        <div>{{ __('attributes.iban') }}: {{ $legalInfo->iban }}</div>
    </div>
</div>

<x-pdf.customer-address :customer="$customer" />

<section class="invoice-meta">
    <div>
        <strong>{{ __('invoices.invoice') }}:
            {{ $invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2)) }}
        </strong>
    </div>
    <div style="text-align: right;">
        <div>{{ __('attributes.customer_no') }}: {{ $customer->customer_no }}</div>
        <div>{{ __('attributes.invoice_no') }}:
            {{ $invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2)) }}
        </div>
        <div>{{ __('translate.date') }}: {{ now()->tz('CET')->format('d.m.Y') }}</div>
        <div>{{ __('attributes.performed_when') }}: {{ $invoice->performed_when ?? sprintf('(%s)', trans('attributes.performed_when')) }}</div>
    </div>
</section>

<table class="line-items">
    <thead>
        <tr>
            <th>{{ __('lineItems.positionShort') }}</th>
            <th class="num">{{ __('attributes.quantity') }}</th>
            <th>{{ __('attributes.price_each') }}</th>
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
                <td>@money($lineItem->price_each, $lineItem->currency)</td>
                <td>{{ $lineItem->unit }}</td>
                <td>
                    <p class="thick">{{ $lineItem->detail }}</p>
                    <p>{{ $lineItem->detail_plus }}</p>
                </td>
                <td class="num">@money($lineItem->without_tax, $lineItem->currency)</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td class="thick">{{ __('attributes.total_without_tax') }}</td>
            <td class="num thick">@money($invoice->total_without_tax, $invoice->currency)</td>
        </tr>
        @foreach($invoice->getTaxDistribution() as $taxRate => $tax)
            <tr>
                <td>{{ $taxRate }}% (@money($tax['without_tax'], $invoice->currency))</td>
                <td class="num">@money($tax['tax'], $invoice->currency)</td>
            </tr>
        @endforeach
        <tr class="grand">
            <td>{{ __('attributes.total_with_tax') }}</td>
            <td class="num">@money($invoice->total_with_tax, $invoice->currency)</td>
        </tr>
    </table>
</div>

<section class="payment">
    <p>Bitte überweisen Sie den vollen Betrag in Höhe von @money($invoice->total_with_tax, $invoice->currency) unter Angabe der
        Rechnungsnummer {{ $invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2)) }}
        bis {{ $invoice->date_due ? $invoice->date_due->format('d.m.Y') : sprintf('(%s)', trans('attributes.date_due')) }}
        auf folgendes Konto:</p>

    <p>{{ __('invoicePdf.reason_for_payment') }}:
        {{ $invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2)) }}
    </p>
    <p>{{ __('attributes.bank_institute') }}: {{ $legalInfo->bank_institute }}</p>
    <p>{{ __('attributes.iban') }}: {{ $legalInfo->iban }}</p>
    <p>{{ __('attributes.swift_bic') }}: {{ $legalInfo->swift_bic }}</p>
</section>

</body>
</html>
