<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{route('tenants.show', ['tenant' => $tenant])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$tenant->id}}" method="post" action="{{route('tenants.destroy', ['tenant' => $tenant])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('tenants.delete_confirm', ['is' => $tenant->name]) }}')) { document.getElementById('delete-{{$tenant->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
