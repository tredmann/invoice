<html>
<head http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <style>

        @page { margin: 50px 50px 100px; }

        body {
            margin-bottom: 15px;
        }

        header {
            border-bottom: 1px solid black;
            font-size: 10px;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            font-size: 10px;
        }

        table {
            width: 100%;
        }

        main table {
            border-collapse: collapse;
        }

        main #line_items th {
            border-bottom: 1px solid black;
            text-align: left;
            font-size: medium;
        }

        main #line_items td {
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            padding-top: 5px;
            font-size: small;
            vertical-align: top;
        }

        header #placeholder_image {
            height: 75px;
            width: auto;
            max-width: 150px;
        }

        div {
            page-break-inside: avoid;
        }

        p {
            margin: 2px;
        }

        .thick {
            font-weight: bold;
        }

    </style>
</head>
<body>
    <footer>
        <table>
            <tr>
                <td style="text-align: left; vertical-align: top">
                    <div>{{ __('attributes.registry_court') }}: {{$legalInfo->registry_court}}</div>
                    <div>{{ __('attributes.registry_no') }}: {{$legalInfo->registry_no}}</div>
                    <div>{{ __('attributes.company_owner') }}: {{$legalInfo->company_owner}}</div>
                </td>
                <td style="text-align: right;  vertical-align: top">
                    <div>{{ __('attributes.tax_no') }}: {{$legalInfo->tax_no}}</div>
                    <div>{{ __('attributes.vat_no') }}: {{$legalInfo->vat_no}}</div>
                    <div>{{ __('attributes.swift_bic') }}: {{$legalInfo->swift_bic}}</div>
                    <div>{{ __('attributes.iban') }}: {{$legalInfo->iban}}</div>
                </td>
            </tr>
        </table>
    </footer>
    <main>

        <header>
            <table>
                <tr>
                    <td>
                        <img id="placeholder_image" src="{{ public_path('images/png/500x261-balt.png') }}"/>
                        <div class="hor-tenant-info">
                            <div>{{$generalInfo->name}} / {{$generalInfo->street}} / {{$generalInfo->postal}} {{$generalInfo->city}}</div>
                        </div>
                    </td>
                    <td style="text-align: right; vertical-align: top">
                        <div class="vert-tenant-info">
                            <div>{{$generalInfo->name}}</div>
                            <div>{{$generalInfo->street}}</div>
                            <div>{{$generalInfo->postal}} {{$generalInfo->city}}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </header>

        <div id="info" style="margin-bottom: 50px">
            <div id="customer_info" style="margin-top: 10px; margin-bottom: 50px;">
                <div id="customer_info">
                    <p>{{$customer->company}}</p>
                    <p>{{$customer->name}}</p>
                    <p>{{$customer->street}}</p>
                    <p>{{$customer->postal}} {{$customer->city}}</p>
                </div>
            </div>
            <div id="invoice_info" style="border-top: 1px solid black; border-bottom: 1px solid black;">
                <table>
                    <tr>
                        <td style="text-align: left; vertical-align: top">
                            <p class="thick">{{ __('invoices.invoice') }}:
                                {{$invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2))}}
                            </p>
                        </td>
                        <td style="text-align: right; vertical-align: top">
                            <p>{{ __('attributes.customer_no') }}: {{$customer->customer_no}}</p>
                            <p>{{ __('attributes.invoice_no') }}:
                                {{$invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2))}}
                            </p>
                            <p>{{ __('translate.date') }}: {{now()->tz('CET')->format('d.m.Y')}}</p>
                            <p>{{ __('attributes.performed_when') }}: {{$invoice->performed_when ?? sprintf('(%s)', trans('attributes.performed_when'))}}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <table id="line_items">
            <thead>
            <tr>
                <th>{{ __('lineItems.positionShort') }}</th>
                <th>{{ __('attributes.quantity') }}</th>
                <th>{{ __('attributes.price_each') }}</th>
                <th>{{ __('attributes.unit') }}</th>
                <th style="width: 50%">{{ __('lineItems.details') }}</th>
                <th style="text-align: right">{{ __('attributes.without_tax') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->lineItems->sortBy('created_at') as $lineItem)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{(str_replace('.', ',', $lineItem->quantity))}}</td>
                    <td>@money($lineItem->price_each, $lineItem->currency)</td>
                    <td>{{$lineItem->unit}}</td>
                    <td>
                        <p class="thick">{{$lineItem->detail}}</p>
                        <p>{{$lineItem->detail_plus}}</p>
                    </td>
                    <td style="text-align: right">@money($lineItem->without_tax, $lineItem->currency)</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div id="calculations">
            <div id="interim_calc" style="margin-top: 10px; margin-bottom: 5px">

                <table>
                    <tr>
                        <td class='thick'>
                            {{ __('attributes.total_without_tax') }}
                        </td>
                        <td style="text-align: right; vertical-align: top" class='thick'>
                            <p>
                                @money($invoice->total_without_tax, $invoice->currency)
                            </p>
                        </td>
                    </tr>
                </table>


                <table>
                    @foreach($invoice->getTaxDistribution() as $taxRate => $tax)
                        <tr>
                            <td>
                                <p>
                                    {{$taxRate}}% (@money($tax['without_tax'], $invoice->currency))
                                </p>
                            </td>
                            <td style="text-align: right; vertical-align: top">
                                <p>
                                    @money($tax['tax'], $invoice->currency)
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </table>

            </div>

            <hr class="medium-thin">

            <div id="final_calc" style="margin-top: 5px">
                <table>
                    <tr>
                        <td class="thick">
                            {{ __('attributes.total_with_tax') }}
                        </td>
                        <td style="text-align: right; vertical-align: top" class='thick'>
                            <p>
                                @money($invoice->total_with_tax, $invoice->currency)
                            </p>
                        </td>
                    </tr>
                </table>

            </div>
        </div>

        <div id="payment">
            <div id="payment_instruction" style="margin-top: 30px">

                <p>Bitte überweisen Sie den vollen Betrag in Höhe von @money($invoice->total_with_tax, $invoice->currency) unter Angabe der
                    Rechnungsnummer {{$invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2))}} bis {{$invoice->date_due ? $invoice->date_due->format('d.m.Y') : sprintf('(%s)', trans('attributes.date_due'))}} auf folgendes Konto:</p>
            </div>

            <div id="payment_details" style="margin-top: 10px">
                <p>{{ __('invoicePdf.reason_for_payment') }}:
                    {{$invoice->invoice_no ?: sprintf('(%s)', $uniqueNumber::predictedNumber($invoice, 2))}}
                </p>
                <p>{{ __('attributes.bank_institute') }}: {{$legalInfo->bank_institute}}</p>
                <p>{{ __('attributes.iban') }}: {{$legalInfo->iban}}</p>
                <p>{{ __('attributes.swift_bic') }}: {{$legalInfo->swift_bic}}</p>
            </div>
        </div>

        <script type="text/php">
        if (isset($pdf))
        {
            $x = 267.5;
            $y = 815;
            $text = "{{ __('invoicePdf.page') }} {PAGE_NUM} {{ __('invoicePdf.of') }}  {PAGE_COUNT}";
            $font = null;
            $size = 8;
            $color = array(0,0,0);

            $h = $pdf->get_height();
            $w = $pdf->get_width();



            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
        </script>
    </main>
</body>
</html>
