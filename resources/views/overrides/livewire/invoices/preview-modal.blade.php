@if ($showInvoicePreviewModal)
    <div class="ek-modal-backdrop" wire:click="closeInvoicePreview">
        <div class="ek-modal ek-invoice-preview-modal" wire:click.stop>
            <div class="ek-invoice-preview-modal__header">
                <h3 class="text-lg font-semibold">{{ __('app.invoices.preview.title') }}</h3>
                <button type="button" wire:click="closeInvoicePreview" class="ek-invoice-preview-close" aria-label="{{ __('app.invoices.preview.close') }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="ek-invoice-preview-scroll">
                @include('livewire.invoices.document-body', [
                    'preview' => $this->invoicePreviewData,
                    'interactiveQr' => true,
                ])
            </div>

            <div class="ek-modal-actions">
                <button type="button" wire:click="closeInvoicePreview" class="ek-btn-secondary" style="width: auto;">{{ __('app.invoices.preview.close_btn') }}</button>
            </div>
        </div>
    </div>
@endif
