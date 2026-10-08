<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? '予約管理システム' }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body>
        {{ $slot }}

        @livewireScripts

        @include('partials.login-expiry')
    </body>
</html>