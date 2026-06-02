<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">

        <a class="navbar-item" href="{{route('tenants.index')}}">
            <img style="max-height: 2.2rem;" src="{{asset('images/png/58x30-balt.png')}}">
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
           data-target="navbar">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbar" class="navbar-menu">
        <div class="navbar-start">

            @if(str_contains(Route::current()->getPrefix(), '{tenant}'))
                <a href="{{ route('dashboard', ['tenant' => $tenant]) }}" class="navbar-item">
                    {{ $tenant->name }}
                </a>

                <a href="{{ route('invoices.index', ['tenant' => $tenant]) }}" class="navbar-item">
                    {{ __('navigation.items.invoices') }}
                </a>

                <a href="{{ route('customers.index', ['tenant' => $tenant]) }}" class="navbar-item">
                    {{ __('navigation.items.customers') }}
                </a>

                <a href="{{ route('tenants.show', ['tenant' => $tenant]) }}" class="navbar-item">
                    Einstellungen
                </a>
            @endif

            <!-- MOBILE -->
            <span class="navbar-item has-text-weight-bold is-group-title is-hidden-desktop">
                {{ __('navigation.profile_context_menu.tenants') }}
            </span>

            <a class="navbar-item is-hidden-desktop is-borderless is-flex is-align-items-baseline is-justify-content-space-between"
               href="{{route('tenants.index')}}">
                {{ __('navigation.profile_context_menu.tenants_index') }}
            </a>

            @if(str_contains(Route::current()->getPrefix(), '{tenant}'))

                @foreach(Auth::user()->tenants as $listTenant)
                    <a class="navbar-item is-hidden-desktop is-borderless is-flex is-align-items-baseline is-justify-content-space-between"
                       href="{{$listTenant->route('customers.index')}}">
                        {{$listTenant->name}}
                        @if($listTenant->id === $tenant->id)
                            <i class="pl-1 fas fa-check-circle"></i>
                        @endif
                    </a>
                @endforeach
                @can('isOwner', \App\Models\Tenant\Tenant::class)
                    <span class="navbar-item has-text-weight-bold is-group-title is-hidden-desktop">{{$tenant->name}}</span>

                    <a class="navbar-item is-hidden-desktop" href="{{route('tenants.users', ['tenant' => $tenant])}}">
                        {{ __('navigation.profile_context_menu.my_tenant') }}
                    </a>
                @endcan

            @else

                @foreach(Auth::user()->tenants as $listTenant)
                    {{--<a class="navbar-item is-hidden-desktop" href="{{route('customers.index', ['tenant' => $listTenant])}}">
                        {{$listTenant->name}}
                    </a>--}}
                    <a class="navbar-item is-hidden-desktop" href="{{$listTenant->route('customers.index')}}">
                        {{$listTenant->name}}
                    </a>
                @endforeach

            @endif

            @can('isAdmin', \App\Models\User::class)
                <span class="navbar-item has-text-weight-bold is-group-title is-hidden-desktop">
                    {{ __('navigation.profile_context_menu.admin') }}
                </span>

                <a class="navbar-item is-hidden-desktop" href="{{route('admin.user-panel.index')}}">
                    {{ __('navigation.profile_context_menu.user_panel') }}
                </a>
            @endcan

            <a class="navbar-item is-hidden-desktop" href="{{ route('logout') }}"
               onclick="event.preventDefault(); $('#logout_{{Auth::id()}}').submit()">
                {{ __('navigation.profile_context_menu.logout') }}

                <i class="pl-1 fas fa-sign-out-alt"></i>
            </a>
        </div>

        <!-- DESKTOP ONLY -->
        <div class="navbar-end is-block-desktop is-hidden-mobile is-hidden-tablet-only">
            <div id="profile_menu_trigger" class="navbar-item">
                <div id="profile_menu_inital" class="initial">
                    {{ Auth::user()->getInitials() }}
                </div>
            </div>
        </div>
        <div id="profile_menu" class="profile-menu is-hidden">
            <div class="profile-menu-group-title profile_section">
                <div class="initial profile_section">
                    {{ Auth::user()->getInitials() }}
                </div>
                <div class="pl-3 profile_section">
                    <p class="has-text-weight-bold profile_section">{{ Auth::user()->name }}</p>
                    <p class="profile_section">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="profile-menu-items">
                <div class="profile-menu-group-title is-borderless has-text-weight-bold">
                    {{ __('navigation.profile_context_menu.tenants') }}
                </div>

                <a href="{{route('tenants.index')}}" class="navbar-item">
                    {{ __('navigation.profile_context_menu.tenants_index') }}
                </a>

                @if(str_contains(Route::current()->getPrefix(), '{tenant}'))
                    @foreach(Auth::user()->tenants as $listTenant)
                        <a class="is-borderless is-flex is-align-items-baseline is-justify-content-space-between"
                           href="{{$listTenant->route('customers.index')}}">
                            {{$listTenant->name}}
                            @if($listTenant->id === $tenant->id)
                                <i class="pl-1 fas fa-check-circle"></i>
                            @endif
                        </a>
                    @endforeach

                    @can('isOwner', \App\Models\Tenant\Tenant::class)
                        <div class="profile-menu-group-title is-borderless has-text-weight-bold">
                            {{$tenant->name}}
                        </div>
                        <a class="is-borderless" href="{{$tenant->route('tenants.users')}}">
                            {{ __('navigation.profile_context_menu.my_tenant') }}
                        </a>
                    @endcan

                @else

                    @foreach(Auth::user()->tenants as $listTenant)
                        <a class="is-borderless" href="{{$listTenant->route('customers.index')}}">
                            {{$listTenant->name}}
                        </a>
                    @endforeach

                @endif

                @can('isAdmin', \App\Models\User::class)
                    <div class="profile-menu-group-title is-borderless has-text-weight-bold">
                        <div>{{ __('navigation.profile_context_menu.admin') }}</div>
                    </div>
                    <a href="{{route('admin.user-panel.index')}}">
                        {{ __('navigation.profile_context_menu.user_panel') }}
                    </a>
                @endcan

                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); $('#logout_{{Auth::id()}}').submit()">
                    {{ __('navigation.profile_context_menu.logout') }}
                    <i class="pl-1 fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- LOGOUT FORM -->
        <form id="logout_{{Auth::id()}}" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
    </div>
</nav>
