@props([])

<div class="relative flex min-h-screen items-center justify-center px-4 py-12">
    <div class="absolute end-4 top-4 z-10">
        <x-theme-toggle />
    </div>

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <h1 class="ek-title-gradient text-2xl font-semibold tracking-tight">
                {{ config('app.name', 'Faktury') }}
            </h1>
            <p class="mt-2 text-sm text-text-muted">Správa faktúr jednoducho a prehľadne</p>
        </div>

        {{ $slot }}
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/envkit-theme.css') }}">
<script src="{{ asset('js/theme.js') }}" defer></script>
