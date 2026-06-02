<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    @if($lineItem->invoice->status === \App\Models\Invoice::STATUS_DRAFT)
        <a href="{{route('lineItems.edit', ['lineItem' => $lineItem, 'tenant' => $tenant])}}">{{ __('translate.edit') }}</a>
    @endif
    <form id="delete-{{$lineItem->id}}" method="post" action="{{route('lineItems.destroy', ['lineItem' => $lineItem, 'tenant' => $tenant])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('lineItems.delete_confirm', ['is' => $lineItem->name]) }}')) { document.getElementById('delete-{{$lineItem->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
