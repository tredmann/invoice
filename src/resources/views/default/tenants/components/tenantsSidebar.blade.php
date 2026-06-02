<button class="sidebar__toggle-trigger" id="sidebar-toggle">
</button>
<aside id="sidebar-content" class="menu">
    <p class="menu-label">
        {{ __('tenants.tenant') }}
    </p>
    <ul class="menu-list">
        <li>
            <a @if(Route::current()->getName() === 'tenants.users') class="is-active" @endif href="{{route('tenants.users', ['tenant' => $tenant])}}">
                {{ __('tenants.users') }}
            </a>
        </li>
        <li>
            <a @if(Route::current()->getName() === 'legalInfos.index') class="is-active" @endif href="{{route('legalInfos.index', ['tenant' => $tenant])}}">
                {{ __('tenants.legalInfos') }}
            </a>
        </li>
        <li>
            <a @if(Route::current()->getName() === 'generalInfos.index') class="is-active" @endif href="{{$tenant->route('generalInfos.index')}}">
                {{ __('tenants.generalInfos') }}
            </a>
        </li>
        <li>
            <a @if(Route::current()->getName() === 'settings.index' || Route::current()->getName() === 'settings.testEmailSettings') class="is-active" @endif href="{{$tenant->route('settings.index')}}">
                {{ __('tenants.settings') }}
            </a>
        </li>
        <li>
            <a @if(Route::current()->getName() === 'tenants.show') class="is-active" @endif href="{{$tenant->route('tenants.show')}}">
                {{ __('tenants.show') }}
            </a>
        </li>
    </ul>
</aside>
