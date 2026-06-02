<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{route('admin.user-panel.edit', ['user' => $user])}}">{{ __('translate.edit') }}</a>
    @if($user->is_admin)
    <form id="demote-{{$user->id}}" method="post" action="{{route('admin.user-panel.demote', ['user' => $user])}}">
        @csrf
        @method('PATCH')
    </form>
    <a href="#"
       onclick="event.preventDefault(); if(confirm('{{ __('admin/user-panel.demote_confirm', ['is' => $user->name]) }}')) { document.getElementById('demote-{{$user->id}}').submit(); }">{{ __('admin/user-panel.demote') }}</a>
    @else
    <form id="promote-{{$user->id}}" method="post" action="{{route('admin.user-panel.promote', ['user' => $user])}}">
        @csrf
        @method('PATCH')
    </form>
    <a href="#"
       onclick="event.preventDefault(); if(confirm('{{ __('admin/user-panel.promote_confirm', ['is' => $user->name]) }}')) { document.getElementById('promote-{{$user->id}}').submit(); }">{{ __('admin/user-panel.promote') }}</a>
    @endif
    <form id="destroy-{{$user->id}}" method="post" action="{{route('admin.user-panel.destroy', ['user' => $user])}}">
        @csrf
        @method('DELETE')
    </form>
    <a href="#"
       onclick="event.preventDefault(); if(confirm('{{ __('admin/user-panel.delete_confirm', ['is' => $user->name]) }}')) { document.getElementById('destroy-{{$user->id}}').submit(); }">{{ __('translate.delete') }}</a>
</div>
