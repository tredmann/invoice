<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <input name="show" type="hidden" value="{{ route('customers.invoices', ['tenant'=> $tenant, 'customer' => $customer]) }}">
    <a href="{{ route('customers.edit', ['customer' => $customer, 'tenant' => $tenant])}}">{{ __('translate.edit') }}</a>
    <form id="delete-{{$customer->id}}" method="post" action="{{route('customers.destroy', ['customer' => $customer, 'tenant' => $tenant])}}">
        @csrf
        @method('DELETE')
        <a href="#" onclick="event.preventDefault(); if(confirm('{{ __('customers.delete_confirm', ['is' => $customer->name]) }}')) { document.getElementById('delete-{{$customer->id}}').submit(); }">{{ __('translate.delete') }}</a>
    </form>
</div>
