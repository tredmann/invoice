<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoices</title>

    @vite(['resources/sass/backend.scss', 'resources/js/jquery.js', 'resources/js/theme.js', 'resources/js/app.js'])
@yield('scripts@Header')
</head>
<body>
<div id="app">

<!-- NAVIGATION -->
@include('layouts.navigation')
<!-- CONTENT -->
@if ($errors->any() || session()->has('success') || session()->has('error'))
    @include('layouts.notification')
@endif

<main class="is-flex">
    @if(isset($sidebarActive) && $sidebarActive)
        <div class="sidebar" id="sidebar">
            @include($sidebar)
        </div>
    @endif
    <div id="content" class="p-5">
        <div class="container">
            @yield('content')
        </div>
    </div>
</main>
<!-- FOOTER -->
@include('layouts.footer')

<!-- SCRIPTS -->
@yield('scripts@Footer')
</div>
</body>
</html>
