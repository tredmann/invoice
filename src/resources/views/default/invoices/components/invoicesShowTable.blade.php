<div class="table-container">
    <table class="table is-fullwidth is-hoverable">
        <tbody>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.invoice_no') }}
            </td>
            <td>
                {{$invoice->invoice_no}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.performed_when') }}
            </td>
            <td>
                {{$invoice->performed_when}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.days_till_due') }}
            </td>
            <td>
                {{$invoice->days_till_due}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.date_due') }}
            </td>
            <td>
                {{$invoice->date_due?->format('d.m.Y')}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.status') }}
            </td>
            <td>
                {{ __('invoices.status.' . $invoice->status, ['invoice_no' => $invoice->cancelledInvoice?->invoice_no]) }}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.open_at') }}
            </td>
            <td>
                {{$invoice->open_at}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.mail_status') }}
            </td>
            <td>
                {{__('invoices.mail_status.'.$invoice->mail_status)}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.invoice_document') }}
            </td>
            <td>
                {{$invoice->getInvoiceDocument()?->path}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.attachment') }}
            </td>
            <td>
                {{$invoice->getAttachment()?->path}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.cancelled_invoice_id') }}
            </td>
            <td>
                @if ($invoice->cancelled_invoice_id)
                    <a href="{{ $tenant->route('invoices.show', ['invoice' => $invoice->cancelled_invoice_id])}}">
                        {{ __('attributes.cancelled_invoice_id') }}
                    </a>
                @endif
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.created_at') }}
            </td>
            <td>
                {{$invoice->created_at->diffInDays(now()) < 1 ? $invoice->created_at->diffForHumans() : $invoice->created_at->format('d.m.Y')}}
            </td>
        </tr>
        <tr>
            <td class="fit has-text-weight-bold">
                {{ __('attributes.updated_at') }}
            </td>
            <td>
                {{$invoice->created_at->diffInDays(now()) < 1 ? $invoice->created_at->diffForHumans() : $invoice->created_at->format('d.m.Y')}}
            </td>
        </tr>
        </tbody>
    </table>
</div>
