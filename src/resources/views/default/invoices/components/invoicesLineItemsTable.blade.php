@if(count($lineItems) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable top">
            <thead>
            <tr>
                <th class="fit">{{ __('lineItems.positionShort') }}</th>
                <th class="fit">{{ __('attributes.quantity') }}</th>
                <th class="fit">{{ __('attributes.price_each') }}</th>
                <th class="fit">{{ __('attributes.unit') }}</th>
                <th>{{ __('lineItems.details') }}</th>
                <th class="fit has-text-right">{{ __('attributes.without_tax') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody>
            @foreach($lineItems->sortBy('created_at') as $lineItem)
                <tr>
                    <td class="fit">
                        {{$loop->iteration}}
                    </td>
                    <td class="fit">
                        {{(str_replace('.', ',', $lineItem->quantity))}}
                    </td>
                    <td class="fit">
                        @money($lineItem->price_each, $lineItem->currency)
                    </td>
                    <td class="fit">
                        {{$lineItem->unit}}
                    </td>
                    <td>
                        <p><b>{{$lineItem->detail}}</b></p>
                        {!! nl2br($lineItem->detail_plus) !!}
                    </td>
                    <td class="fit has-text-right">
                        @money($lineItem->without_tax, $lineItem->currency)
                    </td>
                    <td class="fit">

                        @component('default.invoices.components.invoicesLineItemsTriggerMenu', ['lineItem' => $lineItem, 'tenant' => $tenant, 'invoice' => $invoice])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="table is-fullwidth is-hoverable is-borderless">
            <tbody>
                <tr>
                    <td>{{ __('attributes.total_without_tax') }}</td>
                    <td class="fit has-text-right">
                        @money($invoice->total_without_tax, $lineItem->currency)
                    </td>
                </tr>
                @foreach(\App\Services\Invoices\InvoiceService::totalPerTax($lineItems) as $pair)
                    <tr>
                        <td>
                            @tax($pair['percentage']) (@money($pair['base'], $invoice->currency))
                        </td>
                        <td class="fit has-text-right">
                            @money($pair['value'], $lineItem->currency)
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th>{{ __('attributes.total_with_tax') }}</th>
                    <th class="fit">
                        @money($invoice->total_with_tax, $invoice->currency)
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
@endif
