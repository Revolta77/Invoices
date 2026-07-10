<div class="ek-invoice-form-wrap">
    <div class="ek-invoice-form-header">
        <div class="ek-invoice-form-header__title">
            <button
                type="button"
                wire:click="closeInvoicePanel"
                class="ek-invoice-form-back"
                aria-label="{{ __('app.invoices.actions.close') }}"
            >
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="min-w-0">
                <h2 class="text-xl font-semibold">{{ $isCreatingInvoice ? __('app.invoices.new_title') : __('app.invoices.title') }}</h2>
                <p class="text-sm ek-text-secondary">{{ $invoiceForm['number'] ?? '' }}</p>
            </div>
        </div>
        <div class="ek-invoice-form-actions">
            <button type="button" wire:click="previewInvoice" class="ek-icon-action" title="{{ __('app.invoices.actions.preview') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <span>{{ __('app.invoices.actions.preview') }}</span>
            </button>
            <button type="button" wire:click="printInvoice" class="ek-icon-action" title="{{ __('app.invoices.actions.print') }}" wire:loading.attr="disabled" wire:target="printInvoice,saveInvoice">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>{{ __('app.invoices.actions.print') }}</span>
            </button>
            <button type="button" wire:click="sendInvoice" class="ek-icon-action" title="{{ __('app.invoices.actions.send') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <span>{{ __('app.invoices.actions.send') }}</span>
            </button>
            <button type="button" wire:click="downloadInvoice" class="ek-icon-action" title="{{ __('app.invoices.actions.download') }}" wire:loading.attr="disabled" wire:target="downloadInvoice,saveInvoice">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>{{ __('app.invoices.actions.download') }}</span>
            </button>
            <button type="button" wire:click="duplicateInvoice" class="ek-icon-action" title="{{ __('app.invoices.actions.create_copy') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                <span>{{ __('app.invoices.actions.duplicate') }}</span>
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg px-4 py-3 text-sm" style="border: 1px solid color-mix(in srgb, var(--primary) 35%, var(--border2)); background: color-mix(in srgb, var(--primary) 10%, var(--surface)); color: var(--primary);">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="saveInvoice" class="space-y-4">
        <div class="ek-card p-5 sm:p-6">
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="space-y-4">
                    <div
                        class="relative"
                        x-data="{ open: @entangle('showPartnerSearchResults') }"
                        @click.outside="open = false"
                    >
                        <label for="partner_name" class="ek-label">{{ __('app.invoices.form.partner_name') }}</label>
                        <input wire:model.live.debounce.400ms="invoiceForm.partner_name" wire:focus="focusPartnerField" id="partner_name" type="text" class="ek-input" autocomplete="off" placeholder="{{ __('app.invoices.form.partner_placeholder') }}">
                        @error('invoiceForm.partner_name') <p class="ek-error">{{ $message }}</p> @enderror

                        <div x-show="open" x-cloak class="absolute z-40 mt-1 max-h-64 w-full overflow-auto rounded-lg border shadow-lg" style="border-color: var(--border2); background: var(--surface);">
                            @foreach ($partnerSearchResults as $result)
                                <button type="button" wire:click="selectPartnerFromList({{ $loop->index }})" @click="open = false" class="block w-full px-4 py-3 text-left text-sm transition hover:bg-[var(--surface2)]" style="color: var(--text); border-bottom: 1px solid var(--border2);">
                                    <span class="font-medium">{{ $result['name'] ?? '' }}</span>
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

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="issue_date" class="ek-label">{{ __('app.invoices.form.issue_date') }}</label>
                            <input wire:model.live="invoiceForm.issue_date" id="issue_date" type="date" class="ek-input">
                            @error('invoiceForm.issue_date') <p class="ek-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="delivery_date" class="ek-label">{{ __('app.invoices.form.delivery_date') }}</label>
                            <input wire:model="invoiceForm.delivery_date" id="delivery_date" type="date" class="ek-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="due_days" class="ek-label">{{ __('app.invoices.form.due_days') }}</label>
                            <input wire:model.live="invoiceForm.due_days" id="due_days" type="number" min="0" class="ek-input">
                        </div>
                        <div>
                            <label for="due_date" class="ek-label">{{ __('app.invoices.form.due_date') }}</label>
                            <input wire:model.live="invoiceForm.due_date" id="due_date" type="date" class="ek-input">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm" style="color: var(--text);">
                        <input wire:model="invoiceForm.is_identified_person" type="checkbox" class="ek-checkbox">
                        {{ __('app.invoices.form.identified_person') }}
                    </label>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="invoice_number" class="ek-label">{{ __('app.invoices.form.number') }}</label>
                        <input wire:model="invoiceForm.number" id="invoice_number" type="text" class="ek-input">
                        @error('invoiceForm.number') <p class="ek-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="currency" class="ek-label">{{ __('app.invoices.form.currency') }}</label>
                            <input wire:model="invoiceForm.currency" id="currency" type="text" maxlength="3" class="ek-input" placeholder="{{ __('app.currency_default') }}">
                        </div>
                        <div>
                            <label for="exchange_rate" class="ek-label">{{ __('app.invoices.form.exchange_rate') }}</label>
                            <input wire:model="invoiceForm.exchange_rate" id="exchange_rate" type="text" class="ek-input" placeholder="1,0000">
                        </div>
                    </div>

                    <div>
                        <label for="iban" class="ek-label">{{ __('app.invoices.form.iban') }}</label>
                        <input wire:model="invoiceForm.iban" id="iban" type="text" class="ek-input" list="known-ibans" placeholder="{{ __('app.invoices.form.iban_placeholder') }}" autocomplete="off">
                        <datalist id="known-ibans">
                            @foreach ($this->knownIbans as $iban)
                                <option value="{{ $iban }}"></option>
                            @endforeach
                        </datalist>
                        <p class="mt-1 text-xs ek-text-secondary">{{ __('app.invoices.form.iban_hint') }}</p>
                    </div>

                    <div>
                        <label for="payment_method" class="ek-label">{{ __('app.invoices.form.payment_method') }}</label>
                        <select wire:model="invoiceForm.payment_method" id="payment_method" class="ek-input ek-select">
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="ek-card p-5 sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold">{{ __('app.invoices.form.items') }}</h3>
                <button type="button" wire:click="addInvoiceItem" class="ek-btn-secondary" style="width: auto; padding: 0.375rem 0.75rem;">
                    {{ __('app.invoices.actions.add_item') }}
                </button>
            </div>

            <div class="ek-invoice-items-table-wrap">
                <table class="ek-invoice-items-table">
                    <thead>
                        <tr>
                            <th class="ek-invoice-item-pos">{{ __('app.document.table.position') }}</th>
                            <th>{{ __('app.invoices.form.item_name') }}</th>
                            <th>{{ __('app.invoices.form.quantity') }}</th>
                            <th>{{ __('app.invoices.form.unit') }}</th>
                            <th>{{ __('app.invoices.form.price') }}</th>
                            <th class="ek-invoice-item-total">{{ __('app.invoices.form.total') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoiceItems as $index => $item)
                            <tr wire:key="invoice-item-{{ $index }}">
                                <td class="ek-invoice-item-pos">{{ $index + 1 }}</td>
                                <td>
                                    <input wire:model="invoiceItems.{{ $index }}.name" type="text" class="ek-input ek-input--table">
                                    @error('invoiceItems.'.$index.'.name') <p class="ek-error">{{ $message }}</p> @enderror
                                </td>
                                <td><input wire:model.live="invoiceItems.{{ $index }}.quantity" type="text" inputmode="decimal" class="ek-input ek-input--table"></td>
                                <td><input wire:model="invoiceItems.{{ $index }}.unit" type="text" class="ek-input ek-input--table" placeholder="{{ __('app.unit_default') }}"></td>
                                <td><input wire:model.live="invoiceItems.{{ $index }}.unit_price" type="text" inputmode="decimal" class="ek-input ek-input--table" placeholder="0"></td>
                                <td class="ek-invoice-item-total">{{ number_format((float) ($item['total'] ?? 0), 2, ',', ' ') }}</td>
                                <td>
                                    @if (count($invoiceItems) > 1)
                                        <button type="button" wire:click="removeInvoiceItem({{ $index }})" class="ek-invoice-remove-btn" aria-label="{{ __('app.invoices.actions.remove_item') }}">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($invoiceHasPayment)
            <div class="ek-card p-5 sm:p-6 ek-invoice-payment-summary">
                <h3 class="ek-invoice-payment-summary__title">{{ __('app.invoices.form.payment_section') }}</h3>
                <div class="ek-invoice-payment-summary__grid ek-invoice-payment-summary__grid--form">
                    <div>
                        <label for="invoice-payment-paid-at" class="ek-label">{{ __('app.invoices.form.payment_date') }}</label>
                        <input wire:model="invoicePaymentForm.paid_at" id="invoice-payment-paid-at" type="date" class="ek-input">
                        @error('invoicePaymentForm.paid_at') <p class="ek-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="invoice-payment-method" class="ek-label">{{ __('app.invoices.form.payment_method') }}</label>
                        <select wire:model="invoicePaymentForm.payment_method" id="invoice-payment-method" class="ek-input ek-select">
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                        @error('invoicePaymentForm.payment_method') <p class="ek-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="invoice-payment-amount" class="ek-label">{{ __('app.invoices.form.payment_amount') }}</label>
                        <input wire:model.live="invoicePaymentForm.amount" id="invoice-payment-amount" type="text" inputmode="decimal" class="ek-input">
                        @error('invoicePaymentForm.amount') <p class="ek-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="ek-card p-5 sm:p-6">
            <div class="ek-invoice-form-footer">
                <div class="ek-invoice-form-footer__total">
                    <p class="text-sm ek-text-secondary">{{ __('app.invoices.form.grand_total_label') }}</p>
                    <p class="text-2xl font-semibold">{{ number_format($this->invoiceGrandTotal(), 2, ',', ' ') }} {{ $invoiceForm['currency'] ?? __('app.currency_default') }}</p>
                </div>

                <div class="ek-invoice-form-footer__asset">
                    <label class="flex items-center gap-2 text-sm font-medium" style="color: var(--text);">
                        <input wire:model.live="invoiceForm.logo_enabled" type="checkbox" class="ek-checkbox">
                        {{ __('app.invoices.form.logo') }}
                    </label>
                    @if ($invoiceForm['logo_enabled'] ?? false)
                        @php
                            $initialInvoiceLogoPreview = $existingInvoiceLogoUrl;
                            if ($invoiceLogo) {
                                try { $initialInvoiceLogoPreview = $invoiceLogo->temporaryUrl(); } catch (\Throwable) {}
                            }
                        @endphp

                        <div
                            class="ek-upload ek-upload--compact"
                            wire:key="invoice-logo-{{ md5(($existingInvoiceLogoUrl ?? '').($removeExistingInvoiceLogo ? '-removed' : '')) }}"
                            x-data="{
                                dragging: false,
                                uploading: false,
                                menuOpen: false,
                                previewUrl: @js($initialInvoiceLogoPreview),
                                init() {
                                    const done = () => { this.uploading = false };
                                    this._onUploadDone = (e) => { if (e.detail?.property === 'invoiceLogo') done(); };
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
                            @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0]); const f = $event.dataTransfer.files[0]; if (f?.type === 'image/png') { const dt = new DataTransfer(); dt.items.add(f); $refs.invoiceLogoInput.files = dt.files; $refs.invoiceLogoInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                        >
                            <div class="mb-2 flex items-center justify-end gap-2">
                                <span class="ek-upload-badge">{{ __('app.upload.badge') }}</span>
                            </div>
                            <div class="ek-upload-zone" wire:ignore :class="{ 'ek-upload-zone--dragging': dragging, 'ek-upload-zone--filled': !!previewUrl }" @click="!previewUrl && $refs.invoiceLogoInput.click()">
                                <input
                                    x-ref="invoiceLogoInput"
                                    id="invoice_logo"
                                    type="file"
                                    accept="image/png"
                                    wire:model="invoiceLogo"
                                    class="sr-only"
                                    @change="handleFile($refs.invoiceLogoInput.files[0])"
                                >

                                <div class="ek-upload-preview" x-show="previewUrl" x-cloak>
                                    <img :src="previewUrl" alt="{{ __('app.document.logo_alt') }}" class="ek-upload-preview__img">
                                    <div class="ek-upload-preview__menu" @click.stop @click.outside="menuOpen = false">
                                        <button type="button" class="ek-upload-menu-btn" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-label="{{ __('app.upload.logo_options') }}">
                                            <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                        </button>
                                        <div x-show="menuOpen" x-cloak class="ek-upload-menu-dropdown">
                                            <button type="button" @click="menuOpen = false; $refs.invoiceLogoInput.click()">{{ __('app.upload.change') }}</button>
                                            <button type="button" wire:click="removeInvoiceLogo" class="ek-upload-menu-dropdown__danger" @click="menuOpen = false; previewUrl = null; uploading = false; $refs.invoiceLogoInput.value = ''">{{ __('app.upload.remove') }}</button>
                                        </div>
                                    </div>
                                    <div class="ek-upload-preview__overlay ek-upload-preview__overlay--desktop">
                                        <button type="button" class="ek-upload-btn" @click.stop="$refs.invoiceLogoInput.click()">{{ __('app.upload.change') }}</button>
                                        <button type="button" wire:click="removeInvoiceLogo" class="ek-upload-btn ek-upload-btn--danger" @click.stop="previewUrl = null; uploading = false; $refs.invoiceLogoInput.value = ''">{{ __('app.upload.remove') }}</button>
                                    </div>
                                </div>
                                <div class="ek-upload-empty" x-show="!previewUrl && !uploading">
                                    <div class="ek-upload-empty__icon">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <p class="ek-upload-empty__title">{{ __('app.upload.drag_logo') }}</p>
                                    <p class="ek-upload-empty__hint">{{ __('app.upload.or_select') }} <button type="button" class="ek-upload-link" @click.stop="$refs.invoiceLogoInput.click()">{{ __('app.upload.select_file') }}</button></p>
                                    @if ($existingInvoiceLogoUrl)
                                        <p class="mt-2 text-xs ek-text-secondary">{{ __('app.invoices.form.logo_default_hint') }}</p>
                                    @endif
                                </div>

                                <div class="ek-upload-loading" :class="{ 'ek-upload-loading--active': uploading && !previewUrl }">
                                    <div class="ek-upload-spinner"></div>
                                    <span>{{ __('app.upload.uploading') }}</span>
                                </div>
                            </div>
                            @error('invoiceLogo') <p class="ek-error">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div class="ek-invoice-form-footer__asset">
                    <label class="flex items-center gap-2 text-sm font-medium" style="color: var(--text);">
                        <input wire:model.live="invoiceForm.signature_enabled" type="checkbox" class="ek-checkbox">
                        {{ __('app.invoices.form.stamp') }}
                    </label>
                    @if ($invoiceForm['signature_enabled'] ?? false)
                        @php
                            $initialInvoiceStampPreview = $existingInvoiceStampUrl;
                            if ($invoiceStamp) {
                                try { $initialInvoiceStampPreview = $invoiceStamp->temporaryUrl(); } catch (\Throwable) {}
                            }
                        @endphp

                        <div
                            class="ek-upload ek-upload--compact"
                            wire:key="invoice-stamp-{{ md5(($existingInvoiceStampUrl ?? '').($removeExistingInvoiceStamp ? '-removed' : '')) }}"
                            x-data="{
                                dragging: false,
                                uploading: false,
                                menuOpen: false,
                                previewUrl: @js($initialInvoiceStampPreview),
                                init() {
                                    const done = () => { this.uploading = false };
                                    this._onUploadDone = (e) => { if (e.detail?.property === 'invoiceStamp') done(); };
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
                            @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0]); const f = $event.dataTransfer.files[0]; if (f?.type === 'image/png') { const dt = new DataTransfer(); dt.items.add(f); $refs.invoiceStampInput.files = dt.files; $refs.invoiceStampInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                        >
                            <div class="mb-2 flex items-center justify-end gap-2">
                                <span class="ek-upload-badge">{{ __('app.upload.badge') }}</span>
                            </div>
                            <div class="ek-upload-zone" wire:ignore :class="{ 'ek-upload-zone--dragging': dragging, 'ek-upload-zone--filled': !!previewUrl }" @click="!previewUrl && $refs.invoiceStampInput.click()">
                                <input
                                    x-ref="invoiceStampInput"
                                    id="invoice_stamp"
                                    type="file"
                                    accept="image/png"
                                    wire:model="invoiceStamp"
                                    class="sr-only"
                                    @change="handleFile($refs.invoiceStampInput.files[0])"
                                >

                                <div class="ek-upload-preview" x-show="previewUrl" x-cloak>
                                    <img :src="previewUrl" alt="{{ __('app.document.stamp_alt') }}" class="ek-upload-preview__img">
                                    <div class="ek-upload-preview__menu" @click.stop @click.outside="menuOpen = false">
                                        <button type="button" class="ek-upload-menu-btn" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-label="{{ __('app.upload.stamp_options') }}">
                                            <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                        </button>
                                        <div x-show="menuOpen" x-cloak class="ek-upload-menu-dropdown">
                                            <button type="button" @click="menuOpen = false; $refs.invoiceStampInput.click()">{{ __('app.upload.change') }}</button>
                                            <button type="button" wire:click="removeInvoiceStamp" class="ek-upload-menu-dropdown__danger" @click="menuOpen = false; previewUrl = null; uploading = false; $refs.invoiceStampInput.value = ''">{{ __('app.upload.remove') }}</button>
                                        </div>
                                    </div>
                                    <div class="ek-upload-preview__overlay ek-upload-preview__overlay--desktop">
                                        <button type="button" class="ek-upload-btn" @click.stop="$refs.invoiceStampInput.click()">{{ __('app.upload.change') }}</button>
                                        <button type="button" wire:click="removeInvoiceStamp" class="ek-upload-btn ek-upload-btn--danger" @click.stop="previewUrl = null; uploading = false; $refs.invoiceStampInput.value = ''">{{ __('app.upload.remove') }}</button>
                                    </div>
                                </div>
                                <div class="ek-upload-empty" x-show="!previewUrl && !uploading">
                                    <div class="ek-upload-empty__icon">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <p class="ek-upload-empty__title">{{ __('app.upload.drag_image') }}</p>
                                    <p class="ek-upload-empty__hint">{{ __('app.upload.or_select') }} <button type="button" class="ek-upload-link" @click.stop="$refs.invoiceStampInput.click()">{{ __('app.upload.select_file') }}</button></p>
                                    @if ($existingInvoiceStampUrl)
                                        <p class="mt-2 text-xs ek-text-secondary">{{ __('app.invoices.form.stamp_default_hint') }}</p>
                                    @endif
                                </div>

                                <div class="ek-upload-loading" :class="{ 'ek-upload-loading--active': uploading && !previewUrl }">
                                    <div class="ek-upload-spinner"></div>
                                    <span>{{ __('app.upload.uploading') }}</span>
                                </div>
                            </div>
                            @error('invoiceStamp') <p class="ek-error">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    wire:click="closeInvoicePanel"
                    @class([
                        'ek-btn-secondary',
                        'ek-invoice-form-cancel',
                        'ek-invoice-form-cancel--always' => $isCreatingInvoice,
                    ])
                    style="width: auto;"
                >
                    {{ $isCreatingInvoice ? __('app.invoices.actions.cancel') : __('app.invoices.actions.close_panel') }}
                </button>
                <button type="submit" class="ek-btn-primary" style="width: auto;">{{ __('app.invoices.actions.save') }}</button>
            </div>
        </div>
    </form>

    @include('livewire.invoices.preview-modal')
    @include('livewire.invoices.email-modal')
</div>
