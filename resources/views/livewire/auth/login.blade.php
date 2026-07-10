<div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
    <h2 class="text-xl font-semibold">Prihlásenie</h2>
    <p class="mt-1 text-sm text-slate-600">Prihláste sa do svojho účtu</p>

    <form wire:submit="login" class="mt-6 space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">E-mail</label>
            <input
                wire:model="email"
                id="email"
                type="email"
                autocomplete="email"
                required
                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Heslo</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                autocomplete="current-password"
                required
                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                wire:model="remember"
                id="remember"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            >
            <label for="remember" class="ml-2 text-sm text-slate-600">Zapamätať si ma</label>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="login"
            class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="login">Prihlásiť sa</span>
            <span wire:loading wire:target="login">Prihlasujem...</span>
        </button>
    </form>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-2 text-slate-500">alebo</span>
        </div>
    </div>

    <a
        href="{{ route('auth.google.redirect') }}"
        class="flex w-full items-center justify-center gap-3 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Prihlásiť sa cez Google
    </a>

    <p class="mt-6 text-center text-sm text-slate-600">
        Nemáte účet?
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500" wire:navigate>
            Zaregistrujte sa
        </a>
    </p>
</div>
