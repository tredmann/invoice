<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }}</title>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
</head>
<body>
    <p><a href="{{ route('login') }}">{{ __('auth.buttons.login') }}</a></p>
</body>
</html>
