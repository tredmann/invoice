<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{route('api-tokens.updateForm', ['personalAccessToken' => $apiToken])}}">{{ __('translate.edit') }}</a>
    <form id="destroy-{{$apiToken->id}}" method="post" action="{{route('api-tokens.destroy', ['personalAccessToken' => $apiToken])}}">
        @csrf
        @method('DELETE')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('api-token-manager.delete_confirm') }}')) { document.getElementById('destroy-{{$apiToken->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
