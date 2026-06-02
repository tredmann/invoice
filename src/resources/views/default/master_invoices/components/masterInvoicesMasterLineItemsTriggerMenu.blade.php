<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('masterLineItems.edit', ['masterLineItem' => $masterLineItem])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$masterLineItem->id}}" method="post" action="{{$tenant->route('masterLineItems.destroy', ['masterLineItem' => $masterLineItem])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('masterLineItems.delete_confirm', ['is' => $masterLineItem->name]) }}')) { document.getElementById('delete-{{$masterLineItem->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
