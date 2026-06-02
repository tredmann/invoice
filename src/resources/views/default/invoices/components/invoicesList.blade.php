@if(count($invoices) === 0)
    <div class="has-text-centered p-5">{{ __('translate.no_hits') }}</div>
@else
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th class="fit">{{ __('attributes.status') }}</th>
                <th>{{ __('attributes.invoice_no') }}</th>
                <th class="fit">{{ __('translate.due') }}</th>
                <th class="fit">Erstellt</th>


                <th>Kunde</th>

                <th class="fit has-text-right">{{ __('attributes.total_without_tax') }}</th>
                <th class="fit"><!-- TRIGGER MENU --></th>
            </tr>
            </thead>
            <tbody class="is-clickable">
            @foreach($invoices as $invoice)
                <tr>
                    <td class="fit">
                        @if($invoice->status === \App\Models\Invoice::STATUS_DRAFT)
                            <div class="tag is-light">
                                {{ __('invoices.status.' . $invoice->status) }}
                            </div>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_OPEN)
                            <div class="tag is-warning">
                                {{ __('invoices.status.' . $invoice->status) }}
                            </div>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_OVERDUE)
                            <div class="tag is-danger">
                                {{ __('invoices.status.' . $invoice->status) }}
                            </div>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_PAID)
                            <div class="tag is-success">
                                {{ __('invoices.status.' . $invoice->status) }}
                            </div>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_OPEN_PDF_ERROR)
                            <span class="icon-text is-flex-wrap-nowrap">
                                  <span class="icon">
                                      <i class="fas fa-exclamation-triangle"></i>
                                  </span>
                                  <span>
                                      {{ __('invoices.status.' . $invoice->status) }}
                                 </span>
                            </span>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_CANCELLED)
                            <div class="tag is-dark">
                                {{ __('invoices.status.' . $invoice->status) }}
                            </div>
                        @elseif($invoice->status === \App\Models\Invoice::STATUS_CANCELLATION_INVOICE)
                            <div class="tag is-light">
                                {{ __('invoices.status.' . $invoice->status, ['invoice_no' => $invoice->cancelledInvoice?->invoice_no]) }}
                            </div>
                        @endif
                    </td>
                    <td class="fit">
                        {{$invoice->invoice_no}}
                    </td>

                    <td class="fit">


                        {{ $invoice->date_due?->format('d.m.Y') ?? '-' }}


                    </td>
                    <td class='fit'>
                        {{$invoice->created_at->format('d.m.Y')}}
                    </td>


                    <td>
                        {{ $invoice->customer->company ?? $invoice->customer->name }}
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
