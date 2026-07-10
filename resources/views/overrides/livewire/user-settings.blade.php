<div class="ek-card p-6 sm:p-8">
    <div class="mb-8">
        <h2 class="text-xl font-semibold">{{ __('app.settings.title') }}</h2>
        <p class="mt-1 text-sm ek-text-secondary">{{ __('app.settings.subtitle') }}</p>
    </div>

    @if (session('settings-status'))
        <div class="mb-6 rounded-lg px-4 py-3 text-sm" style="border: 1px solid color-mix(in srgb, var(--primary) 35%, var(--border2)); background: color-mix(in srgb, var(--primary) 10%, var(--surface)); color: var(--primary);">
            {{ session('settings-status') }}
        </div>
    @endif

    @error('google')
        <div class="mb-6 rounded-lg px-4 py-3 text-sm" style="border: 1px solid color-mix(in srgb, var(--danger) 35%, var(--border2)); background: color-mix(in srgb, var(--danger) 10%, var(--surface)); color: var(--danger);">
            {{ $message }}
        </div>
    @enderror

    <section class="space-y-4 border-b pb-8" style="border-color: var(--border2);">
        <h3 class="text-base font-semibold">{{ __('app.settings.email.section') }}</h3>
        <form wire:submit="saveEmail" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="settings-email" class="ek-label">{{ __('app.settings.email.label') }}</label>
                <input wire:model="email" id="settings-email" type="email" class="ek-input" required>
                @error('email') <p class="ek-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="ek-btn-secondary" style="width: auto; min-width: 8rem;">{{ __('app.settings.email.submit') }}</button>
        </form>
    </section>

    <section class="space-y-4 border-b py-8" style="border-color: var(--border2);">
        <h3 class="text-base font-semibold">{{ __('app.settings.password.section') }}</h3>
        @if (filled(auth()->user()->password))
            <form wire:submit="updatePassword" class="grid max-w-xl gap-4">
                <div>
                    <label for="current_password" class="ek-label">{{ __('app.settings.password.current') }}</label>
                    <input wire:model="current_password" id="current_password" type="password" class="ek-input" autocomplete="current-password">
                    @error('current_password') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="ek-label">{{ __('app.settings.password.new') }}</label>
                    <input wire:model="password" id="password" type="password" class="ek-input" autocomplete="new-password">
                    @error('password') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="ek-label">{{ __('app.settings.password.confirmation') }}</label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" class="ek-input" autocomplete="new-password">
                </div>
                <button type="submit" class="ek-btn-secondary" style="width: auto; min-width: 8rem;">{{ __('app.settings.password.submit') }}</button>
            </form>
        @else
            <p class="text-sm ek-text-secondary">
                {{ __('app.settings.password.google_only') }}
            </p>
        @endif
    </section>

    <section class="space-y-4 pt-8">
        <h3 class="text-base font-semibold">{{ __('app.google.section_title') }}</h3>
        <p class="text-sm ek-text-secondary">
            {{ __('app.google.description') }}
            @if (config('google-drive.backup_enabled'))
                {{ __('app.google.backup_auto') }}
            @else
                {{ __('app.google.backup_disabled') }}
            @endif
            {{ __('app.google.import_hint') }}
        </p>

        @if (filled(auth()->user()->google_id))
            <div class="flex flex-col gap-4 rounded-lg border p-4" style="border-color: var(--border2); background: var(--surface2);">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="" class="h-10 w-10 rounded-full">
                        @endif
                        <div>
                            <p class="text-sm font-medium">{{ __('app.google.linked') }}</p>
                            <p class="text-xs ek-text-secondary">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="importGoogleBackup"
                            wire:loading.attr="disabled"
                            wire:confirm="{{ __('app.google.import_confirm') }}"
                            class="ek-btn-secondary"
                            style="width: auto;"
                            @disabled(! auth()->user()->canSyncToGoogleDrive())
                        >
                            <span wire:loading.remove wire:target="importGoogleBackup">{{ __('app.google.import') }}</span>
                            <span wire:loading wire:target="importGoogleBackup">{{ __('app.google.importing') }}</span>
                        </button>
                        <button type="button" wire:click="unlinkGoogle" class="ek-btn-secondary" style="width: auto; color: var(--danger);">
                            {{ __('app.google.unlink') }}
                        </button>
                    </div>
                </div>

                @if (auth()->user()->canSyncToGoogleDrive())
                    <div class="text-xs ek-text-secondary space-y-1">
                        @if (auth()->user()->google_backup_last_at)
                            <p>{{ __('app.google.last_backup', ['date' => auth()->user()->google_backup_last_at->format('d.m.Y H:i')]) }}</p>
                        @endif
                        @if (auth()->user()->google_backup_status === 'syncing')
                            <p style="color: var(--primary);">{{ __('app.google.syncing') }}</p>
                        @elseif (auth()->user()->google_backup_status === 'failed')
                            <p style="color: var(--danger);">{{ __('app.google.sync_failed', ['error' => auth()->user()->google_backup_error]) }}</p>
                        @elseif (auth()->user()->google_backup_status === 'success')
                            <p style="color: var(--primary);">{{ __('app.google.sync_success') }}</p>
                        @else
                            <p>{{ __('app.google.sync_pending') }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-xs" style="color: var(--danger);">
                        {{ __('app.google.refresh_token_missing') }}
                    </p>
                @endif
            </div>
        @else
            <button type="button" wire:click="linkGoogle" class="ek-btn-secondary" style="width: auto;">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('app.google.link') }}
            </button>
        @endif
    </section>
</div>
