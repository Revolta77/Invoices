<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        @livewireStyles
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="flex min-h-screen items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ config('app.name', 'Faktury') }}</h1>
                    <p class="mt-2 text-sm text-slate-600">Správa faktúr jednoducho a prehľadne</p>
                </div>

                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
