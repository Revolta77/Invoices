<div class="min-h-screen">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
            <div>
                <h1 class="text-lg font-semibold">{{ config('app.name', 'Faktury') }}</h1>
                <p class="text-sm text-slate-600">Vitajte späť, {{ auth()->user()->name }}</p>
            </div>

            <div class="flex items-center gap-4">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                    {{ auth()->user()->role->label() }}
                </span>

                <button
                    wire:click="logout"
                    type="button"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Odhlásiť sa
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-xl font-semibold">Dashboard</h2>
            <p class="mt-2 text-slate-600">
                Ste prihlásený ako <strong>{{ auth()->user()->email }}</strong>.
                V ďalšom kroku tu pridáme správu faktúr.
            </p>

            @if (auth()->user()->isAdmin())
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    Máte rolu administrátora. Neskôr tu pribudne správa používateľov a systémové nastavenia.
                </div>
            @endif
        </div>
    </main>
</div>
