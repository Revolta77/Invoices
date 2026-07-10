<div class="ek-card p-8">
    <h2 class="text-xl font-semibold">{{ __('app.auth.reset_password.title') }}</h2>
    <p class="mt-1 text-sm ek-text-secondary">{{ __('app.auth.reset_password.subtitle') }}</p>

    <form wire:submit="resetPassword" class="mt-6 space-y-5">
        <div>
            <label for="email" class="ek-label">{{ __('app.auth.reset_password.email') }}</label>
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

        <div>
            <label for="password" class="ek-label">{{ __('app.auth.reset_password.password') }}</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                autocomplete="new-password"
                required
                class="ek-input"
            >
            @error('password')
                <p class="ek-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="ek-label">{{ __('app.auth.reset_password.password_confirmation') }}</label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="ek-input"
            >
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="resetPassword"
            class="ek-btn-primary"
        >
            <span wire:loading.remove wire:target="resetPassword">{{ __('app.auth.reset_password.submit') }}</span>
            <span wire:loading wire:target="resetPassword">{{ __('app.auth.reset_password.submitting') }}</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm ek-text-secondary">
        <a href="{{ route('login') }}" class="ek-link" wire:navigate>{{ __('app.auth.reset_password.back_to_login') }}</a>
    </p>
</div>
