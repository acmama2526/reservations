<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>予約管理システム</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body>
    <x-header />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
</body>
</html>