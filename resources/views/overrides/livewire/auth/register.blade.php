<div class="ek-card p-8">
    <h2 class="text-xl font-semibold">{{ __('app.auth.register.title') }}</h2>
    <p class="mt-1 text-sm ek-text-secondary">{{ __('app.auth.register.subtitle') }}</p>

    <form wire:submit="register" class="mt-6 space-y-5">
        <div>
            <label for="name" class="ek-label">{{ __('app.auth.register.name') }}</label>
            <input wire:model="name" id="name" type="text" autocomplete="name" required class="ek-input">
            @error('name')<p class="ek-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="ek-label">{{ __('app.auth.register.email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required class="ek-input">
            @error('email')<p class="ek-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="ek-label">{{ __('app.auth.register.password') }}</label>
            <input wire:model="password" id="password" type="password" autocomplete="new-password" required class="ek-input">
            @error('password')<p class="ek-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="ek-label">{{ __('app.auth.register.password_confirmation') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" required class="ek-input">
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="register" class="ek-btn-primary">
            <span wire:loading.remove wire:target="register">{{ __('app.auth.register.submit') }}</span>
            <span wire:loading wire:target="register">{{ __('app.auth.register.submitting') }}</span>
        </button>
    </form>

    <div class="ek-divider"><span>{{ __('app.or') }}</span></div>

    <a href="{{ route('auth.google.redirect') }}" class="ek-btn-secondary">
        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        {{ __('app.auth.register.google') }}
    </a>

    <p class="mt-6 text-center text-sm ek-text-secondary">
        {{ __('app.auth.register.has_account') }}
        <a href="{{ route('login') }}" class="ek-link" wire:navigate>{{ __('app.auth.register.login_link') }}</a>
    </p>
</div>
