<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $showVerificationModal = false;

    public string $unverifiedEmail = '';

    public int $resendCooldown = 0;

    public bool $verificationSent = false;

    public function login(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => __('app.validation.auth.email_required'),
            'email.email' => __('app.validation.auth.email_invalid'),
            'password.required' => __('app.validation.auth.password_required'),
        ]);

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('app.validation.auth.invalid_credentials'),
            ]);
        }

        $user = Auth::user();

        if ($user && ! $user->hasVerifiedEmail()) {
            Auth::logout();

            $this->unverifiedEmail = $user->email;
            $this->showVerificationModal = true;
            $this->verificationSent = false;
            $this->refreshResendCooldown();

            return;
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function resendVerificationEmail(): void
    {
        if ($this->unverifiedEmail === '') {
            return;
        }

        $this->refreshResendCooldown();

        if ($this->resendCooldown > 0) {
            return;
        }

        $user = User::query()->where('email', $this->unverifiedEmail)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        RateLimiter::hit($this->resendThrottleKey(), (int) config('auth.verification.resend_throttle', 60));

        $this->verificationSent = true;
        $this->refreshResendCooldown();
    }

    public function closeVerificationModal(): void
    {
        $this->showVerificationModal = false;
        $this->unverifiedEmail = '';
        $this->resendCooldown = 0;
        $this->verificationSent = false;
    }

    public function refreshResendCooldown(): void
    {
        if ($this->unverifiedEmail === '') {
            $this->resendCooldown = 0;

            return;
        }

        $this->resendCooldown = RateLimiter::tooManyAttempts($this->resendThrottleKey(), 1)
            ? RateLimiter::availableIn($this->resendThrottleKey())
            : 0;
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('app.validation.auth.rate_limited', ['seconds' => $seconds]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    protected function resendThrottleKey(): string
    {
        return 'verify-resend:'.Str::lower($this->unverifiedEmail);
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->title(__('app.auth.login.title'));
    }
}
