@if(count($paidInvoices) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.paid_at') }}</th>
                <th class="fit">{{ __('attributes.invoice_no') }}</th>
                <th>{{ __('attributes.company') }}</th>
                <th class="fit">{{ __('attributes.total_without_tax') }}</th>
                <th><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($paidInvoices as $invoice)
                <tr>
                    <td class="fit">
                        <div class="tag">
                            {{$invoice->paid_at?->format('d.m.Y')}}
                        </div>
                    </td>
                    <td class="fit">
                        {{$invoice->invoice_no}}
                    </td>
                    <td>
                        {{$invoice->customer->company}}
                    </td>
                    <td class="fit has-text-right">
                        <div>
                            @money($invoice->total_without_tax, $invoice->currency)
                        </div>
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">
                        @component('default.invoices.components.triggerMenu', ['invoice' => $invoice, 'tenant' => $tenant])
                        @endcomponent
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
