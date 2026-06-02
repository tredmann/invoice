<button class="sidebar__toggle-trigger" id="sidebar-toggle">
</button>
<aside id="sidebar-content" class="menu">
    <p class="menu-label">
        {{ __('customers.customer') }}
    </p>
    <ul class="menu-list">
        <li>
            <a @if(str_contains(Route::current()->getName(), 'invoices') || str_contains(Route::current()->getName(), 'lineItems')
                ) class="is-active"
               @endif href="{{route('customers.invoices', ['customer' => $customer, 'tenant' => $customer->tenant])}}">
                {{ __('customers.invoices') }}
            </a>
        </li>
        <li>
            <a @if(str_contains(Route::current()->getName(), 'masterInvoices') ||str_contains(Route::current()->getName(), 'masterLineItems'))
                   class="is-active" @endif
            href="{{route('customers.masterInvoices', ['customer' => $customer, 'tenant' => $customer->tenant])}}">
                {{ __('customers.master_invoices') }}
            </a>
        </li>
        <li>
            <a @if(str_contains(Route::current()->getName(), 'mailReceivers')) class="is-active" @endif
            href="{{route('customers.mailReceivers', ['customer' => $customer, 'tenant' => $customer->tenant])}}">
                {{ __('customers.mail_receivers') }}
            </a>
        </li>
        <li>
            <a @if(Route::current()->getName() === 'customers.show') class="is-active" @endif
            href="{{ route('customers.show', ['customer' => $customer, 'tenant' => $customer->tenant])}}">
                {{ __('customers.show') }}
            </a>
        </li>
    </ul>
</aside>

