<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <input name="show" type="hidden" value="{{$tenant->route('masterInvoices.masterLineItems', ['masterInvoice' => $masterInvoice])}}">
    @if($masterInvoice->status !== \App\Models\MasterInvoice::STATUS_ACTIVE)
        <a href="{{$tenant->route('masterInvoices.activate', ['masterInvoice' => $masterInvoice])}}">{{ __('masterInvoices.active') }}</a>
    @else
        <form id="pause-{{$masterInvoice->id}}" method="post" action="{{$tenant->route('masterInvoices.pause', ['masterInvoice' => $masterInvoice])}}">
            @csrf
            @method('PATCH')
        </form>
        <a href="#" onclick="event.preventDefault(); if(confirm('{{ __('masterInvoices.pause_confirm') }}')) { document.getElementById('pause-{{$masterInvoice->id}}').submit(); }">{{ __('masterInvoices.pause') }}</a>
    @endif
    <form id="delete-{{$masterInvoice->id}}" method="post" action="{{$tenant->route('masterInvoices.destroy', ['masterInvoice' => $masterInvoice])}}">
        @csrf
        @method('DELETE')
    </form>
    <a href="#" onclick="event.preventDefault(); if(confirm('{{ __('masterInvoices.delete_confirm') }}')) { document.getElementById('delete-{{$masterInvoice->id}}').submit(); }">{{ __('translate.delete') }}</a>
</div>
