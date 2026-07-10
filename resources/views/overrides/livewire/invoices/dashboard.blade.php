<div
    @class([
        'ek-invoice-dashboard',
        'ek-invoice-dashboard--panel-open' => $selectedInvoiceId || $isCreatingInvoice,
    ])
    x-data="{
        syncInvoicePanelBodyClass() {
            const open = $wire.selectedInvoiceId !== null || $wire.isCreatingInvoice;
            document.body.classList.toggle(
                'ek-body--invoice-panel',
                open && window.matchMedia('(max-width: 1023px)').matches
            );
        },
    }"
    x-init="syncInvoicePanelBodyClass()"
    x-on:resize.window="syncInvoicePanelBodyClass()"
    x-effect="syncInvoicePanelBodyClass()"
>
    <div class="ek-invoice-dashboard__main">
        @if ($selectedInvoiceId || $isCreatingInvoice)
            @include('livewire.invoices.form')
        @else
            <div class="ek-card ek-invoice-empty">
                <div class="ek-invoice-empty__content">
                    <svg class="ek-invoice-empty__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-lg font-semibold">{{ __('app.invoices.dashboard.empty_title') }}</h2>
                    <p class="mt-2 text-sm ek-text-secondary">
                        {{ __('app.invoices.dashboard.empty_description') }}
                    </p>
                </div>
            </div>
        @endif
    </div>

    <aside class="ek-invoice-dashboard__sidebar">
        @include('livewire.invoices.list-panel')
    </aside>
</div>
