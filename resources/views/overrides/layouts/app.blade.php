<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            })();
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        <link rel="stylesheet" href="{{ asset('css/envkit-theme.css') }}">
        <script src="{{ asset('js/theme.js') }}" defer></script>
        @livewireStyles
    </head>
    <body class="ek-page min-h-screen">
        {{ $slot }}

        @livewireScripts
        <script src="{{ asset('js/form-errors.js') }}"></script>
        <script src="{{ asset('js/invoice-document.js') }}" defer></script>
    </body>
</html>
