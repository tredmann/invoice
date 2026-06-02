<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <!-- Show -->
    <input name="show" type="hidden" value="{{ route('invoices.show', ['invoice' => $invoice, 'tenant' => $tenant])}}">

    <!-- Show Pdf -->
    <a href="{{ route('invoices.pdf', ['invoice' => $invoice, 'tenant' => $tenant])}}" target="_blank">{{ __('invoices.show_pdf') }}</a>

    <!-- Conclude -->
    @if ($invoice->status === $invoice::STATUS_DRAFT || $invoice->status === $invoice::STATUS_OPEN_PDF_ERROR)
        <a href="{{route('invoices.conclude', ['invoice' => $invoice, 'tenant' => $tenant])}}" id="set-invoice-open">{{ __('invoices.conclude') }}</a>
    @endif


    <!-- Cancel Invoice -->
    @if ($invoice->status !== $invoice::STATUS_DRAFT && $invoice->status !== $invoice::STATUS_CANCELLED && $invoice->status !== $invoice::STATUS_CANCELLATION_INVOICE)
        <form id="invoice-cancel-{{$invoice->id}}" method="post" action="{{route('invoices.cancel', ['invoice' => $invoice, 'tenant' => $tenant])}}">
            @csrf
            @method('PATCH')

        </form>
        <a href="" onclick="event.preventDefault(); {document.getElementById('invoice-cancel-{{$invoice->id}}').submit();}">{{ __('invoices.cancel') }}</a>
    @endif

    <!-- Send Mail -->
    @if ($invoice->status !== $invoice::STATUS_DRAFT)
        <div class="p-3">
            <form id="invoice-sendmail-{{$invoice->id}}" method="post" enctype="multipart/form-data"
                  action="{{route('invoices.sendmail', ['customer' => $invoice->customer, 'invoice' => $invoice, 'tenant' => $tenant])}}">
                @csrf
                <span>{{ __('invoices.mail_attachment') }}</span>
                <input name="mail_attachment" type="file" class="mx-5">
                <button type="submit"
                        onclick="event.preventDefault(); {document.getElementById('invoice-sendmail-{{$invoice->id}}').submit();}">{{ __('invoices.send') }}</button>
            </form>
        </div>
    @endif

    <!-- Status Paid -->
    @if ($invoice->status === $invoice::STATUS_OPEN || $invoice->status === $invoice::STATUS_OVERDUE)
        <div class="p-3">
            <form id="invoice-paid-{{$invoice->id}}" method="post" action="{{$tenant->route('invoices.paid', ['invoice' => $invoice])}}">
                @csrf
                @method('PATCH')

                <span>{{ __('invoices.paid') }}</span>
                <input name="paid_at" type="date" value="{{ today()->format('Y-m-d') }}">
                <button onclick="event.preventDefault();  if(confirm('Möchten Sie die Rechnung als bezahlt markieren?')) {document.getElementById('invoice-paid-{{$invoice->id}}').submit();}">{{ __('translate.save') }}</button>
            </form>
        </div>
    @endif

    <!-- Delete -->
    @if($invoice->status === $invoice::STATUS_DRAFT)
        <form id="delete-{{$invoice->id}}" method="post" action="{{ route('invoices.destroy', ['invoice' => $invoice, 'tenant' => $tenant])}}">
            @csrf
            @method('DELETE')
            <a href="#" onclick="event.preventDefault(); if(confirm('{{ __('invoices.delete_confirm', ['is' => $invoice->name]) }}')) { document.getElementById('delete-{{$invoice->id}}').submit(); }">{{ __('translate.delete') }}</a>
        </form>
    @endif

</div>
