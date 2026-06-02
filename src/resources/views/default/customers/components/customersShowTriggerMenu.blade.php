<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <a href="{{$tenant->route('customers.edit', ['customer' => $customer])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$customer->id}}" method="post" action="{{$tenant->route('customers.destroy', ['customer' => $customer])}}">
        @csrf
        @method('DELETE')
        <a href="#" onclick="event.preventDefault(); if(confirm('{{ __('customers.delete_confirm', ['is' => $customer->name]) }}')) { document.getElementById('delete-{{$customer->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
