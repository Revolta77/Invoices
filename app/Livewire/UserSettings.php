<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\GoogleDriveBackupImporter;
use App\Support\GoogleDriveBackupDispatcher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UserSettings extends Component
{
    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $importingBackup = false;

    public function mount(): void
    {
        $this->email = auth()->user()->email;
    }

    public function saveEmail(): void
    {
        $validated = $this->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
        ], [
            'email.required' => __('app.validation.settings.email_required'),
            'email.email' => __('app.validation.settings.email_invalid'),
            'email.unique' => __('app.validation.settings.email_unique'),
        ]);

        auth()->user()->update(['email' => $validated['email']]);
        $this->email = $validated['email'];

        session()->flash('settings-status', __('app.messages.email_updated'));
    }

    public function updatePassword(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->password) {
            throw ValidationException::withMessages([
                'current_password' => __('app.validation.settings.no_password_set'),
            ]);
        }

        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => __('app.validation.settings.current_password_required'),
            'current_password.current_password' => __('app.validation.settings.current_password_invalid'),
            'password.required' => __('app.validation.settings.password_required'),
            'password.confirmed' => __('app.validation.settings.password_confirmed'),
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('settings-status', __('app.messages.password_changed'));
    }

    public function linkGoogle(): void
    {
        session(['google_link_intent' => true]);
        $this->redirect('/auth/google/link', navigate: false);
    }

    public function unlinkGoogle(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->google_id) {
            return;
        }

        if (! $user->password) {
            throw ValidationException::withMessages([
                'google' => __('app.validation.settings.google_unlink_password'),
            ]);
        }

        $user->update([
            'google_id' => null,
            'avatar' => null,
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_drive_root_folder_id' => null,
            'google_backup_status' => null,
            'google_backup_error' => null,
        ]);

        session()->flash('settings-status', __('app.messages.google_unlinked'));
    }

    public function importGoogleBackup(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->canSyncToGoogleDrive()) {
            throw ValidationException::withMessages([
                'google' => __('app.validation.settings.google_link_required'),
            ]);
        }

        $this->importingBackup = true;

        try {
            $result = app(GoogleDriveBackupImporter::class)->import($user);

            GoogleDriveBackupDispatcher::dispatch($user);

            session()->flash(
                'settings-status',
                __('app.messages.google_import_completed', [
                    'profiles' => $result['profiles'],
                    'invoices' => $result['invoices'],
                ])
            );
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'google' => __('app.validation.settings.google_import_failed', ['error' => $exception->getMessage()]),
            ]);
        } finally {
            $this->importingBackup = false;
        }
    }

    public function render()
    {
        return view('livewire.user-settings');
    }
}
