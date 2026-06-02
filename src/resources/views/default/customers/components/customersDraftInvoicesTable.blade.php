@if(count($drafts) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.status') }}</th>
                <th>{{ __('attributes.updated_at') }}</th>
                <th class="fit">{{ __('attributes.total_without_tax') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($drafts as $draft)
                <tr>
                    <td>
                        @if($draft->status === \App\Models\Invoice::STATUS_DRAFT)
                            <div class="tag is-primary">
                                {{ __('invoices.status.' . $draft->status) }}
                            </div>
                        @elseif($draft->status === \App\Models\Invoice::STATUS_OPEN)
                            <div class="tag is-warning">
                                {{ __('invoices.status.' . $draft->status) }}
                            </div>
                        @elseif($draft->status === \App\Models\Invoice::STATUS_OVERDUE)
                            <div class="tag is-danger">
                                {{ __('invoices.status.' . $draft->status) }}
                            </div>
                        @elseif($draft->status === \App\Models\Invoice::STATUS_PAID)
                            <div class="tag is-success">
                                {{ __('invoices.status.' . $draft->status) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        {{$draft->updated_at->diffInDays(now()) < 1 ? $draft->updated_at->diffForHumans() : $draft->updated_at->format('d.m.Y')}}
                    </td>
                    <td class="fit has-text-right">
                        <div>
                            @money($draft->total_without_tax, $draft->currency)
                        </div>
                    </td>
                    <td class="fit" onclick="event.stopPropagation();">

                        @component('default.invoices.components.triggerMenu', ['invoice' => $draft, 'customer' => $customer, 'tenant' => $tenant])
                        @endcomponent

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
