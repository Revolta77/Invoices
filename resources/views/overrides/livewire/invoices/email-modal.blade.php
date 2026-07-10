@if ($showInvoiceEmailModal)
    <div class="ek-modal-backdrop" wire:click="closeInvoiceEmailModal">
        <div class="ek-modal ek-invoice-email-modal" wire:click.stop>
            <h3 class="text-lg font-semibold">{{ __('app.invoices.email.title') }}</h3>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="invoice-email-to" class="ek-label">{{ __('app.invoices.email.to') }}</label>
                    <input wire:model="invoiceEmailForm.to" id="invoice-email-to" type="email" class="ek-input" placeholder="{{ __('app.invoices.email.to_placeholder') }}" autocomplete="off">
                    @error('invoiceEmailForm.to') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-cc" class="ek-label">{{ __('app.invoices.email.cc') }}</label>
                    <input wire:model="invoiceEmailForm.cc" id="invoice-email-cc" type="text" class="ek-input" placeholder="{{ __('app.invoices.email.cc_placeholder') }}" autocomplete="off">
                    @error('invoiceEmailForm.cc') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-from" class="ek-label">{{ __('app.invoices.email.from') }}</label>
                    <input wire:model="invoiceEmailForm.from" id="invoice-email-from" type="email" class="ek-input" autocomplete="off">
                    <p class="mt-1 text-xs ek-text-secondary">
                        {{ __('app.invoices.email.from_hint') }}
                    </p>
                    @error('invoiceEmailForm.from') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-locale" class="ek-label">{{ __('app.invoices.email.locale') }}</label>
                    <select wire:model.live="invoiceEmailForm.locale" id="invoice-email-locale" class="ek-input">
                        @foreach (config('locales.available', []) as $code => $locale)
                            <option value="{{ $code }}">{{ $locale['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs ek-text-secondary">{{ __('app.invoices.email.locale_hint') }}</p>
                    @error('invoiceEmailForm.locale') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-subject" class="ek-label">{{ __('app.invoices.email.subject') }}</label>
                    <input wire:model="invoiceEmailForm.subject" id="invoice-email-subject" type="text" class="ek-input">
                    @error('invoiceEmailForm.subject') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-body" class="ek-label">{{ __('app.invoices.email.body') }}</label>
                    <textarea wire:model="invoiceEmailForm.body" id="invoice-email-body" rows="7" class="ek-input" style="resize: vertical;"></textarea>
                    <p class="mt-1 text-xs ek-text-secondary">
                        {{ __('app.invoices.email.body_hint') }}
                    </p>
                    @error('invoiceEmailForm.body') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice-email-attachment" class="ek-label">{{ __('app.invoices.email.attachment') }}</label>
                    <input wire:model="emailExtraAttachment" id="invoice-email-attachment" type="file" class="ek-input" style="padding: 0.45rem 0.75rem;">
                    @error('emailExtraAttachment') <p class="ek-error">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs ek-text-secondary">{{ __('app.invoices.email.attachment_hint') }}</p>
                </div>

                @error('invoiceEmail') <p class="ek-error">{{ $message }}</p> @enderror

                <div class="ek-modal-actions">
                    <button type="button" wire:click="closeInvoiceEmailModal" class="ek-btn-secondary" style="width: auto;" wire:loading.attr="disabled" wire:target="sendInvoiceEmail">{{ __('app.invoices.email.close') }}</button>
                    <button type="button" wire:click="sendInvoiceEmail" class="ek-btn-primary" style="width: auto;" wire:loading.attr="disabled" wire:target="sendInvoiceEmail">
                        <span wire:loading.remove wire:target="sendInvoiceEmail">{{ __('app.invoices.email.submit') }}</span>
                        <span wire:loading wire:target="sendInvoiceEmail">{{ __('app.invoices.email.submitting') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
