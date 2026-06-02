<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Balt-Abrechnungen') }}</title>

    @vite(['resources/sass/backend.scss', 'resources/js/jquery.js'])
@yield('scripts@Header')
</head>
<body>
    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

@yield('scripts@Footer')
</body>
</html>
