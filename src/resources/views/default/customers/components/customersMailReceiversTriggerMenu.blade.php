<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('customerMailReceivers.edit', ['customerMailReceiver' => $mailReceiver])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$mailReceiver->id}}" method="post" action="{{$tenant->route('customerMailReceivers.destroy', ['customerMailReceiver' => $mailReceiver])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('customerMailReceivers.delete_confirm', ['is' => $mailReceiver->name]) }}')) { document.getElementById('delete-{{$mailReceiver->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>


