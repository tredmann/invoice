<!-- TRIGGER MENU -->
<i class="fas fa-ellipsis-v context-menu-trigger"></i>
<div class="context-menu context-menu-hidden box p-0">
    <input name="show" type="hidden" value="{{route('masterInvoices.masterLineItems', ['masterInvoice' => $masterInvoice, 'tenant' => $tenant])}}">
</div>
