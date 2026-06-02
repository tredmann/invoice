<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}}</title>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    @vite(['resources/css/custom.css'])

</head>
<body>
<div id="app">
    <div class="main">
        <header class="header">
            <div class="header--menu">
                <div class="header--item header--item__logo">
                    <img  height="30" width="auto" src="{{ asset('images/png/58x30-balt.png') }}"/>
                </div>
                @auth
                    <a class="header--link" href="{{ route('tenants.index') }}">
                        <div
                            class="header--item @if(Str::startsWith(Route::current()->getName(), 'tenants')) header--item__active @endif ">
                            Tenants
                        </div>
                    </a>
                    @if(Str::startsWith(Route::current()->getPrefix(), '{tenant}'))
                        <a class="header--link" href="{{ $tenant->route('customers.index') }}">
                            <div
                                class="header--item @if(Str::startsWith(Route::current()->getName(), 'customers')) header--item__active @endif ">
                                Kunden
                            </div>
                        </a>
                    @endif
                @endauth
            </div>
            <div class="header--actions">
                @auth

                    <div class="menu">

                        <div class="menu--header-item">
                            <div
                                class="menu--trigger">{{Auth()->user()->email}}</div>
                        </div>

                        <div class="menu--items">

                            @if(Str::startsWith(Route::current()->getPrefix(), '{tenant}'))

                                <div class="menu--item__group menu--item__bordertop">
                                    <div>Tenants</div>
                                </div>
                                @foreach(Auth::user()->tenants as $tenant)
                                    <div class="menu--item">
                                        <a href="{{ $tenant->route('customers.index') }}">
                                            {{$tenant->name}}
                                        </a>
                                    </div>
                                @endforeach

                                @can('isOwner', \App\Models\Tenant\Tenant::class)
                                    <div class="menu--item__group menu--item__bordertop">
                                        <div>{{$tenant->name}}</div>
                                    </div>

                                    <div class="menu--item">
                                        <a href="{{ $tenant->route('legalInfos.index') }}">
                                            Rechtliche Angaben
                                        </a>
                                    </div>
                                    <div class="menu--item">
                                        <a href="{{ $tenant->route('generalInfos.index') }}">
                                            Allgemeine Angaben
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @can('isAdmin', \App\Models\User::class)
                                <div class="menu--item__group menu--item__bordertop">
                                    <div>Admin</div>
                                </div>
                                <div class="menu--item">
                                    <a href="{{ route('admin.user-panel.index') }}">
                                        Nutzerverwaltung
                                    </a>
                                </div>
                            @endcan

                            <div class="menu--item">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                            </div>

                        </div>
                    </div>
                @endauth

            </div>
        </header>

        <main>
            @yield('content')
        </main>

    </div>
</div>


<script>

    function copyToClipboard(txt) {
        var $temp = jQuery("<input>");
        jQuery("body").append($temp);
        $temp.val(txt).select();
        document.execCommand("copy");
        $temp.remove();
    }

    // without jQuery (doesn't work in older IEs)
    document.addEventListener('DOMContentLoaded', function () {

        window.setTimeout(function () {
            jQuery('.notification').fadeOut(400);
        }, 3000);

        jQuery('.context-menu--trigger').click(function () {
            var content = $(this).siblings('.context-menu--content');
            if (content.hasClass('context-menu--content__show')) {
                content.removeClass('context-menu--content__show');
            } else {
                jQuery('.context-menu--content').removeClass('context-menu--content__show');
                content.addClass('context-menu--content__show');
            }
        });

        jQuery('.menu--trigger').click(function () {
            var content = $(this).parent().siblings('.menu--items');

            if (content.hasClass('menu--items__show')) {
                content.removeClass('menu--items__show');
            } else {
                jQuery('.menu--items').removeClass('menu--items__show');
                content.addClass('menu--items__show');
            }
        });

        jQuery(window).click(function (event) {

            if (!event.target.matches('.context-menu--trigger')) {
                jQuery('.context-menu--content').removeClass('context-menu--content__show');
            }

            if (!event.target.matches('.menu--trigger')) {
                jQuery('.menu--items').removeClass('menu--items__show');
                jQuery('.menu--trigger').removeClass('menu--trigger__show');
            }
        });


    }, false);

</script>

@yield('scripts@Footer')

</body>
</html>
