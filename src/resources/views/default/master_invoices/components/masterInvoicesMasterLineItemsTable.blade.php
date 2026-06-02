@if(count($masterLineItems) === 0)
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
            @foreach($masterLineItems->sortBy('created_at') as $masterLineItem)
                <tr>
                    <td class="fit">
                        {{$loop->iteration}}
                    </td>
                    <td class="fit">
                        {{(str_replace('.', ',', $masterLineItem->quantity))}}
                    </td>
                    <td class="fit">
                        @money($masterLineItem->price_each, $masterLineItem->currency)
                    </td>
                    <td class="fit">
                        {{$masterLineItem->unit}}
                    </td>
                    <td>
                        <p><b>{{$masterLineItem->detail}}</b></p>
                        {{$masterLineItem->detail_plus}}
                    </td>
                    <td class="fit has-text-right">
                        @money($masterLineItem->price_each, $masterLineItem->currency)
                    </td>
                    <td class="fit">

                        @component('default.master_invoices.components.masterInvoicesMasterLineItemsTriggerMenu', ['masterLineItem' => $masterLineItem, 'tenant' => $tenant])
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
                    @money($masterInvoice->total_without_tax, $masterLineItem->currency)
                </td>
            </tr>
            @foreach(\App\Services\Invoices\InvoiceService::totalPerTax($masterLineItems) as $pair)
                <tr>
                    <td>
                        @tax($pair['percentage']) (@money($pair['base'], $masterInvoice->currency))
                    </td>
                    <td class="fit has-text-right">
                        @money($pair['value'], $masterLineItem->currency)
                    </td>
                </tr>
            @endforeach
            <tr>
                <th>{{ __('attributes.total_with_tax') }}</th>
                <th class="fit">
                    @money($masterInvoice->total_with_tax, $masterInvoice->currency)
                </th>
            </tr>
            </tbody>
        </table>
    </div>
@endif
