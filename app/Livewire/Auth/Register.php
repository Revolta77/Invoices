<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ], [
            'name.required' => __('app.validation.auth.name_required'),
            'email.required' => __('app.validation.auth.email_required'),
            'email.email' => __('app.validation.auth.email_invalid'),
            'email.unique' => __('app.validation.auth.email_unique'),
            'password.required' => __('app.validation.auth.password_required'),
            'password.confirmed' => __('app.validation.auth.password_confirmed'),
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::User,
        ]);

        $user->sendEmailVerificationNotification();

        session()->flash('status', __('app.messages.registration_check_email'));

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->title(__('app.auth.register.title'));
    }
}
