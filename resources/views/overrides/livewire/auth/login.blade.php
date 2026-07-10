<div>
    <div class="ek-card p-8">
        <h2 class="text-xl font-semibold">{{ __('app.auth.login.title') }}</h2>
        <p class="mt-1 text-sm ek-text-secondary">{{ __('app.auth.login.subtitle') }}</p>

        @if (session('status'))
            <div class="ek-alert ek-alert--success mt-4" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if (session('verification-error'))
            <div class="ek-alert ek-alert--error mt-4" role="alert">
                {{ session('verification-error') }}
            </div>
        @endif

        <form wire:submit="login" class="mt-6 space-y-5">
            <div>
                <label for="email" class="ek-label">{{ __('app.auth.login.email') }}</label>
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
                <div class="mb-1.5 flex items-center justify-between gap-3">
                    <label for="password" class="ek-label mb-0">{{ __('app.auth.login.password') }}</label>
                    <a href="{{ route('password.request') }}" class="ek-link text-sm" wire:navigate>
                        {{ __('app.auth.login.forgot_password') }}
                    </a>
                </div>
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="ek-input"
                >
                @error('password')
                    <p class="ek-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input
                    wire:model="remember"
                    id="remember"
                    type="checkbox"
                    class="ek-checkbox"
                >
                <label for="remember" class="text-sm ek-text-secondary">{{ __('app.auth.login.remember') }}</label>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="ek-btn-primary"
            >
                <span wire:loading.remove wire:target="login">{{ __('app.auth.login.submit') }}</span>
                <span wire:loading wire:target="login">{{ __('app.auth.login.submitting') }}</span>
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
            {{ __('app.auth.login.google') }}
        </a>

        <p class="mt-6 text-center text-sm ek-text-secondary">
            {{ __('app.auth.login.no_account') }}
            <a href="{{ route('register') }}" class="ek-link" wire:navigate>{{ __('app.auth.login.register_link') }}</a>
        </p>
    </div>

    @if ($showVerificationModal)
        <div class="ek-modal-backdrop" wire:click="closeVerificationModal" wire:poll.1s="refreshResendCooldown">
            <div class="ek-modal" wire:click.stop>
                <h3 class="text-lg font-semibold">{{ __('app.auth.verification.modal_title') }}</h3>
                <p class="mt-2 text-sm ek-text-secondary">
                    {{ __('app.auth.verification.modal_description', ['email' => $unverifiedEmail]) }}
                </p>

                @if ($verificationSent)
                    <div class="ek-alert ek-alert--success mt-4" role="status">
                        {{ __('app.auth.verification.resent') }}
                    </div>
                @endif

                <p class="mt-4 text-sm ek-text-secondary">
                    {{ __('app.auth.verification.expires_hint', ['minutes' => config('auth.verification.expire', 5)]) }}
                </p>

                <div class="ek-modal-actions mt-6">
                    <button type="button" class="ek-btn-secondary" wire:click="closeVerificationModal">
                        {{ __('app.auth.verification.close') }}
                    </button>
                    <button
                        type="button"
                        class="ek-btn-primary"
                        wire:click="resendVerificationEmail"
                        wire:loading.attr="disabled"
                        wire:target="resendVerificationEmail"
                        @disabled($resendCooldown > 0)
                    >
                        @if ($resendCooldown > 0)
                            {{ __('app.auth.verification.resend_wait', ['seconds' => $resendCooldown]) }}
                        @else
                            <span wire:loading.remove wire:target="resendVerificationEmail">{{ __('app.auth.verification.resend') }}</span>
                            <span wire:loading wire:target="resendVerificationEmail">{{ __('app.auth.verification.resending') }}</span>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
