<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('legalInfos.edit', ['legalInfo' => $legalInfo])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$legalInfo->id}}" method="post" action="{{$tenant->route('legalInfos.destroy', ['legalInfo' => $legalInfo])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('legalInfos.delete_confirm', ['is' => $legalInfo->name]) }}')) { document.getElementById('delete-{{$legalInfo->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
