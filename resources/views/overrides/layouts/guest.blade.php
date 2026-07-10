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
        <div class="relative flex min-h-screen items-center justify-center px-4 py-12">
            <div class="absolute end-4 top-4 z-10 flex items-center gap-2">
                <x-locale-toggle />
                <x-theme-toggle />
            </div>

            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="ek-title-gradient text-2xl font-semibold tracking-tight">
                        {{ config('app.name', 'Faktury') }}
                    </h1>
                    <p class="mt-2 text-sm ek-muted">{{ __('app.tagline') }}</p>
                </div>

                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
