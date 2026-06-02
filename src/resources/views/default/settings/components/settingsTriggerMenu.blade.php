<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('settings.edit', ['setting' => $setting])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$setting->id}}" method="post" action="{{$tenant->route('settings.destroy', ['setting' => $setting])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('settings.delete_confirm', ['is' => $setting->key]) }}')) { document.getElementById('delete-{{$setting->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
