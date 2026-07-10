<div class="ek-card p-6 sm:p-8">
    <div class="mb-8">
        <h2 class="text-xl font-semibold">
            {{ $this->isEditingProfile() ? __('app.company.edit_title') : __('app.company.create_title') }}
        </h2>
        <p class="mt-1 text-sm ek-text-secondary">
            {{ __('app.company.subtitle') }}
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg px-4 py-3 text-sm" style="border: 1px solid color-mix(in srgb, var(--primary) 35%, var(--border2)); background: color-mix(in srgb, var(--primary) 10%, var(--surface)); color: var(--primary);">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="saveProfile" class="space-y-8">
        <section>
            <h3 class="text-base font-semibold">{{ __('app.company.sections.basic') }}</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div
                    class="relative sm:col-span-2"
                    x-data="{ open: @entangle('showSearchResults') }"
                    @click.outside="open = false"
                >
                    <label for="name" class="ek-label">{{ __('app.company.fields.name') }}</label>
                    <input wire:model.live.debounce.400ms="name" id="name" type="text" class="ek-input" autocomplete="off" placeholder="{{ __('app.company.fields.name_placeholder') }}">
                    @error('name') <p class="ek-error">{{ $message }}</p> @enderror

                    <div
                        x-show="open"
                        x-cloak
                        class="absolute z-40 mt-1 max-h-64 w-full overflow-auto rounded-lg border shadow-lg"
                        style="border-color: var(--border2); background: var(--surface);"
                    >
                        @foreach ($searchResults as $result)
                            <button type="button" wire:click="selectCompany('{{ $result['ico'] }}')" @click="open = false" class="block w-full px-4 py-3 text-left text-sm transition hover:bg-[var(--surface2)]" style="color: var(--text); border-bottom: 1px solid var(--border2);">
                                <span class="font-medium">{{ $result['name'] }}</span>
                                @if (! empty($result['city']))
                                    <span class="ml-2 ek-text-secondary">{{ $result['city'] }}</span>
                                @endif
                                @if (! empty($result['ico']))
                                    <span class="ml-2 ek-text-secondary">{{ __('app.document.ico') }} {{ $result['ico'] }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="street" class="ek-label">{{ __('app.company.fields.street') }}</label>
                    <input wire:model="street" id="street" type="text" class="ek-input">
                    @error('street') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="postal_code" class="ek-label">{{ __('app.company.fields.postal_code') }}</label>
                    <input wire:model="postal_code" id="postal_code" type="text" class="ek-input">
                    @error('postal_code') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="city" class="ek-label">{{ __('app.company.fields.city') }}</label>
                    <input wire:model="city" id="city" type="text" class="ek-input">
                    @error('city') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="country" class="ek-label">{{ __('app.company.fields.country') }}</label>
                    <input wire:model="country" id="country" type="text" maxlength="2" class="ek-input" placeholder="SK">
                    @error('country') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ico" class="ek-label">{{ __('app.company.fields.ico') }}</label>
                    <input wire:model="ico" id="ico" type="text" class="ek-input">
                    @error('ico') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="dic" class="ek-label">{{ __('app.company.fields.dic') }}</label>
                    <input wire:model="dic" id="dic" type="text" class="ek-input">
                    @error('dic') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="taxpayer_type" class="ek-label">{{ __('app.company.fields.taxpayer_type') }}</label>
                    <select wire:model="taxpayer_type" id="taxpayer_type" class="ek-input ek-select">
                        @foreach ($taxpayerTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('taxpayer_type') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ic_dph" class="ek-label">{{ __('app.company.fields.ic_dph') }}</label>
                    <input wire:model="ic_dph" id="ic_dph" type="text" class="ek-input">
                    @error('ic_dph') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-base font-semibold">{{ __('app.company.sections.registry') }}</h3>
            <div class="mt-4">
                <label for="registry" class="ek-label">{{ __('app.company.fields.registry') }}</label>
                <textarea wire:model="registry" id="registry" rows="2" class="ek-input"></textarea>
                @error('registry') <p class="ek-error">{{ $message }}</p> @enderror
            </div>
        </section>

        <section>
            <h3 class="text-base font-semibold">{{ __('app.company.sections.contact') }}</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="email" class="ek-label">{{ __('app.company.fields.email') }}</label>
                    <input wire:model="email" id="email" type="email" class="ek-input">
                    @error('email') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="ek-label">{{ __('app.company.fields.phone') }}</label>
                    <input wire:model="phone" id="phone" type="text" class="ek-input">
                    @error('phone') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="web" class="ek-label">{{ __('app.company.fields.web') }}</label>
                    <input wire:model="web" id="web" type="url" class="ek-input" placeholder="https://">
                    @error('web') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-base font-semibold">{{ __('app.company.sections.logo_stamp') }}</h3>
            <p class="mt-1 text-sm ek-text-secondary">{{ __('app.company.logo_stamp_hint') }}</p>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                @php
                    $initialLogoPreview = $existingLogoUrl;
                    if ($logo) {
                        try { $initialLogoPreview = $logo->temporaryUrl(); } catch (\Throwable) {}
                    }
                    $initialStampPreview = $existingStampUrl;
                    if ($stamp) {
                        try { $initialStampPreview = $stamp->temporaryUrl(); } catch (\Throwable) {}
                    }
                @endphp

                {{-- Logo --}}
                <div
                    class="ek-upload"
                    x-data="{
                        dragging: false,
                        uploading: false,
                        previewUrl: @js($initialLogoPreview),
                        init() {
                            const done = () => { this.uploading = false };
                            this._onUploadDone = (e) => { if (e.detail?.property === 'logo') done(); };
                            window.addEventListener('livewire-upload-finish', this._onUploadDone);
                            window.addEventListener('livewire-upload-error', this._onUploadDone);
                            window.addEventListener('livewire-upload-cancel', this._onUploadDone);
                        },
                        destroy() {
                            window.removeEventListener('livewire-upload-finish', this._onUploadDone);
                            window.removeEventListener('livewire-upload-error', this._onUploadDone);
                            window.removeEventListener('livewire-upload-cancel', this._onUploadDone);
                        },
                        handleFile(file) {
                            if (!file || file.type !== 'image/png') return;
                            this.uploading = true;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previewUrl = e.target.result;
                                this.uploading = false;
                            };
                            reader.readAsDataURL(file);
                        }
                    }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0]); const f = $event.dataTransfer.files[0]; if (f?.type === 'image/png') { const dt = new DataTransfer(); dt.items.add(f); $refs.logoInput.files = dt.files; $refs.logoInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="ek-label" style="margin: 0;">{{ __('app.company.fields.logo') }}</span>
                        <span class="ek-upload-badge">{{ __('app.upload.badge') }}</span>
                    </div>
                    <div class="ek-upload-zone" wire:ignore :class="{ 'ek-upload-zone--dragging': dragging, 'ek-upload-zone--filled': !!previewUrl }" @click="!previewUrl && $refs.logoInput.click()">
                        <input
                            x-ref="logoInput"
                            id="logo"
                            type="file"
                            accept="image/png"
                            wire:model="logo"
                            class="sr-only"
                            @change="handleFile($refs.logoInput.files[0])"
                        >

                        <div class="ek-upload-preview" x-show="previewUrl" x-cloak>
                                <img :src="previewUrl" alt="{{ __('app.document.logo_alt') }}" class="ek-upload-preview__img">
                                <div class="ek-upload-preview__overlay">
                                    <button type="button" class="ek-upload-btn" @click.stop="$refs.logoInput.click()">{{ __('app.upload.change') }}</button>
                                    <button type="button" wire:click="removeLogo" class="ek-upload-btn ek-upload-btn--danger" @click.stop="previewUrl = null; uploading = false; $refs.logoInput.value = ''">{{ __('app.upload.remove') }}</button>
                                </div>
                            </div>
                            <div class="ek-upload-empty" x-show="!previewUrl && !uploading">
                                <div class="ek-upload-empty__icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <p class="ek-upload-empty__title">{{ __('app.upload.drag_image') }}</p>
                                <p class="ek-upload-empty__hint">{{ __('app.upload.or_select') }} <button type="button" class="ek-upload-link" @click.stop="$refs.logoInput.click()">{{ __('app.upload.select_file') }}</button></p>
                            </div>

                        <div class="ek-upload-loading" :class="{ 'ek-upload-loading--active': uploading && !previewUrl }">
                            <div class="ek-upload-spinner"></div>
                            <span>{{ __('app.upload.uploading') }}</span>
                        </div>
                    </div>
                    @error('logo') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                {{-- Pečiatka --}}
                <div
                    class="ek-upload"
                    x-data="{
                        dragging: false,
                        uploading: false,
                        previewUrl: @js($initialStampPreview),
                        init() {
                            const done = () => { this.uploading = false };
                            this._onUploadDone = (e) => { if (e.detail?.property === 'stamp') done(); };
                            window.addEventListener('livewire-upload-finish', this._onUploadDone);
                            window.addEventListener('livewire-upload-error', this._onUploadDone);
                            window.addEventListener('livewire-upload-cancel', this._onUploadDone);
                        },
                        destroy() {
                            window.removeEventListener('livewire-upload-finish', this._onUploadDone);
                            window.removeEventListener('livewire-upload-error', this._onUploadDone);
                            window.removeEventListener('livewire-upload-cancel', this._onUploadDone);
                        },
                        handleFile(file) {
                            if (!file || file.type !== 'image/png') return;
                            this.uploading = true;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previewUrl = e.target.result;
                                this.uploading = false;
                            };
                            reader.readAsDataURL(file);
                        }
                    }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0]); const f = $event.dataTransfer.files[0]; if (f?.type === 'image/png') { const dt = new DataTransfer(); dt.items.add(f); $refs.stampInput.files = dt.files; $refs.stampInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="ek-label" style="margin: 0;">{{ __('app.company.fields.stamp') }}</span>
                        <span class="ek-upload-badge">{{ __('app.upload.badge') }}</span>
                    </div>
                    <div class="ek-upload-zone" wire:ignore :class="{ 'ek-upload-zone--dragging': dragging, 'ek-upload-zone--filled': !!previewUrl }" @click="!previewUrl && $refs.stampInput.click()">
                        <input
                            x-ref="stampInput"
                            id="stamp"
                            type="file"
                            accept="image/png"
                            wire:model="stamp"
                            class="sr-only"
                            @change="handleFile($refs.stampInput.files[0])"
                        >

                        <div class="ek-upload-preview" x-show="previewUrl" x-cloak>
                                <img :src="previewUrl" alt="{{ __('app.document.stamp_alt') }}" class="ek-upload-preview__img">
                                <div class="ek-upload-preview__overlay">
                                    <button type="button" class="ek-upload-btn" @click.stop="$refs.stampInput.click()">{{ __('app.upload.change') }}</button>
                                    <button type="button" wire:click="removeStamp" class="ek-upload-btn ek-upload-btn--danger" @click.stop="previewUrl = null; uploading = false; $refs.stampInput.value = ''">{{ __('app.upload.remove') }}</button>
                                </div>
                            </div>
                            <div class="ek-upload-empty" x-show="!previewUrl && !uploading">
                                <div class="ek-upload-empty__icon">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <p class="ek-upload-empty__title">{{ __('app.upload.drag_image') }}</p>
                                <p class="ek-upload-empty__hint">{{ __('app.upload.or_select') }} <button type="button" class="ek-upload-link" @click.stop="$refs.stampInput.click()">{{ __('app.upload.select_file') }}</button></p>
                            </div>

                        <div class="ek-upload-loading" :class="{ 'ek-upload-loading--active': uploading && !previewUrl }">
                            <div class="ek-upload-spinner"></div>
                            <span>{{ __('app.upload.uploading') }}</span>
                        </div>
                    </div>
                    @error('stamp') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="flex flex-col gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between" style="border-color: var(--border2);">
            @if ($this->isEditingProfile())
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-sm ek-text-secondary">
                        <input wire:model="confirmDelete" type="checkbox" class="ek-checkbox">
                        {{ __('app.company.delete_confirm') }}
                    </label>
                    <button type="button" wire:click="deleteProfile" wire:loading.attr="disabled" @disabled(! $confirmDelete) class="ek-btn-secondary" style="width: auto; color: var(--danger); border-color: color-mix(in srgb, var(--danger) 35%, var(--border2));">
                        {{ __('app.company.delete') }}
                    </button>
                </div>
                <button type="submit" wire:loading.attr="disabled" @disabled(! $this->isProfileFormDirty()) class="ek-btn-primary sm:ml-auto" style="width: auto; min-width: 10rem;">
                    <span wire:loading.remove wire:target="saveProfile">{{ __('app.company.save') }}</span>
                    <span wire:loading wire:target="saveProfile">{{ __('app.company.saving') }}</span>
                </button>
            @else
                <button type="submit" wire:loading.attr="disabled" class="ek-btn-primary sm:ml-auto" style="width: auto; min-width: 10rem;">
                    <span wire:loading.remove wire:target="saveProfile">{{ __('app.company.create') }}</span>
                    <span wire:loading wire:target="saveProfile">{{ __('app.company.creating') }}</span>
                </button>
            @endif
        </div>
    </form>
</div>
