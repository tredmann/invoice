<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <form id="remove-user-{{$tenantUser->id}}" method="post" action="{{ route('tenants.remove-user', ['user' => $tenantUser, 'tenant' => $tenant])}}">
        @csrf
        @method('PATCH')
        <a href="#"
           onclick="event.preventDefault(); if(confirm('{{ __('tenants.remove_user_confirm', ['is' => $tenantUser->name]) }}')) { document.getElementById('remove-user-{{$tenantUser->id}}').submit(); }">{{ __('tenants.remove_user') }}</a>
    </form>
</div>
