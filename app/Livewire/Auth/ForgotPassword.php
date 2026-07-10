<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => __('app.validation.auth.email_required'),
            'email.email' => __('app.validation.auth.email_invalid'),
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        session()->flash('status', __('app.auth.forgot_password.sent'));

        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->title(__('app.auth.forgot_password.title'));
    }
}
