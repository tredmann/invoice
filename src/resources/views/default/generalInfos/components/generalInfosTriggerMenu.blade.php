<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('generalInfos.edit', ['generalInfo' => $generalInfo])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$generalInfo->id}}" method="post" action="{{$tenant->route('generalInfos.destroy', ['generalInfo' => $generalInfo])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('generalInfos.delete_confirm', ['is' => $generalInfo->name]) }}')) { document.getElementById('delete-{{$generalInfo->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
