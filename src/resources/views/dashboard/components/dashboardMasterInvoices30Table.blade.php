@if(count($masterInvoicesSentWithin30Days) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('dashboard.due_in') }}</th>
                <th>{{ __('attributes.company') }}</th>
                <th class="fit">{{ __('attributes.total_without_tax') }}</th>
                <th><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($masterInvoicesSentWithin30Days as $masterInvoice)
                <tr>
                    <td class="fit">
                        <div class="tag">
                            {{$masterInvoice->next_print->diffInDays(now()) < 1 ? $masterInvoice->next_print->longAbsoluteDiffForHumans() : $masterInvoice->next_print->format('d.m.Y')}}
                        </div>
                    </td>
                    <td>
                        {{$masterInvoice->customer->company}}
                    </td>
                    <td class="fit has-text-right">
                        @money($masterInvoice->total_without_tax, $masterInvoice->currency)
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">
                        @component('dashboard.components.dashboardMasterInvoiceTriggerMenu', ['masterInvoice' => $masterInvoice, 'tenant' => $tenant])
                        @endcomponent
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
