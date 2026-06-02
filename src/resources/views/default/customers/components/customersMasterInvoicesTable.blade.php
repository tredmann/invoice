@if(count($masterInvoices) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.next_print') }}</th>
                <th>{{ __('attributes.status') }}</th>
                <th class="fit">{{ __('attributes.total_without_tax') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($masterInvoices as $masterInvoice)
                <tr>
                    <td class="fit">
                        <div class="tag">
                            {{$masterInvoice->next_print->diffInDays(now()) < 1 ? $masterInvoice->next_print->diffForHumans() : $masterInvoice->next_print->format('d.m.Y')}}
                        </div>
                    </td>
                    <td>
                        @if($masterInvoice->status === \App\Models\MasterInvoice::STATUS_DRAFT)
                            <div class="tag is-primary">
                                {{ __('masterInvoices.status.' . $masterInvoice->status) }}
                            </div>
                        @elseif($masterInvoice->status === \App\Models\MasterInvoice::STATUS_PAUSED)
                            <div class="tag is-warning">
                                {{ __('masterInvoices.status.' . $masterInvoice->status) }}
                            </div>
                        @elseif($masterInvoice->status === \App\Models\MasterInvoice::STATUS_ACTIVE)
                            <div class="tag is-success">
                                {{ __('masterInvoices.status.' . $masterInvoice->status) }}
                            </div>
                        @endif
                    </td>
                    <td class="fit has-text-right">
                        <div>
                            @money($masterInvoice->total_without_tax, $masterInvoice->currency)
                        </div>
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">

                        @component('default.customers.components.customersMasterInvoicesTriggerMenu', ['masterInvoice' => $masterInvoice, 'tenant' => $tenant])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
