<div class="ek-card p-8">
    <h2 class="text-xl font-semibold">{{ __('app.auth.forgot_password.title') }}</h2>
    <p class="mt-1 text-sm ek-text-secondary">{{ __('app.auth.forgot_password.subtitle') }}</p>

    @if (session('status'))
        <div class="ek-alert ek-alert--success mt-4" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendResetLink" class="mt-6 space-y-5">
        <div>
            <label for="email" class="ek-label">{{ __('app.auth.forgot_password.email') }}</label>
            <input
                wire:model="email"
                id="email"
                type="email"
                autocomplete="email"
                required
                class="ek-input"
            >
            @error('email')
                <p class="ek-error">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="sendResetLink"
            class="ek-btn-primary"
        >
            <span wire:loading.remove wire:target="sendResetLink">{{ __('app.auth.forgot_password.submit') }}</span>
            <span wire:loading wire:target="sendResetLink">{{ __('app.auth.forgot_password.submitting') }}</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm ek-text-secondary">
        <a href="{{ route('login') }}" class="ek-link" wire:navigate>{{ __('app.auth.forgot_password.back_to_login') }}</a>
    </p>
</div>
