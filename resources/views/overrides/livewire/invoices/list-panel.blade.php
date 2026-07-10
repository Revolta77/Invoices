@php
    use App\InvoiceStatus;

    $filteredInvoices = $this->filteredInvoices;
    $paidSum = $filteredInvoices->where('status', InvoiceStatus::Paid)->sum('total');
    $unpaidSum = $filteredInvoices
        ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Overdue])
        ->sum('total');
@endphp

<div class="ek-card ek-invoice-list-card">
    <div class="ek-invoice-list-card__header">
        <label for="invoice-search" class="sr-only">{{ __('app.invoices.list.search_label') }}</label>
        <div class="ek-invoice-search-wrap">
            <svg class="ek-invoice-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                wire:model.live.debounce.300ms="invoiceSearch"
                id="invoice-search"
                type="search"
                class="ek-input ek-invoice-search-input"
                placeholder="{{ __('app.invoices.list.search_placeholder') }}"
                autocomplete="off"
            >
        </div>

        <button type="button" wire:click="startCreateInvoice" class="ek-btn-primary ek-invoice-create-btn">
            {{ __('app.invoices.list.create') }}
        </button>

        <div class="ek-invoice-list-toolbar">
            <button type="button" wire:click="openFilterModal" class="ek-btn-secondary ek-invoice-toolbar-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                {{ __('app.invoices.list.filter') }}
            </button>
            <button type="button" wire:click="openSortModal" class="ek-btn-secondary ek-invoice-toolbar-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                {{ __('app.invoices.list.sort') }}
            </button>
        </div>
    </div>

    <div class="ek-invoice-list-scroll">
        @forelse ($filteredInvoices as $invoice)
            <div
                wire:click="selectInvoice({{ $invoice->id }})"
                wire:keydown.enter="selectInvoice({{ $invoice->id }})"
                role="button"
                tabindex="0"
                class="ek-invoice-list-item {{ $selectedInvoiceId === $invoice->id ? 'ek-invoice-list-item--active' : '' }}"
            >
                <div class="ek-invoice-list-item__row ek-invoice-list-item__row--head">
                    <div class="ek-invoice-list-item__head-meta">
                        <span class="ek-invoice-list-item__number">{{ $invoice->number }}</span>
                        <span class="ek-invoice-list-item__sep" aria-hidden="true">|</span>
                        <span class="ek-invoice-list-item__date">{{ $invoice->issue_date->format('d.m.Y') }}</span>
                    </div>
                    <div class="ek-invoice-list-item__menu" x-data="{ open: false }" @click.outside="open = false" @click.stop>
                        <button type="button" @click="open = !open" class="ek-invoice-menu-btn" aria-label="{{ __('app.invoices.list.menu') }}">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                        </button>
                        <div x-show="open" x-cloak class="ek-invoice-menu-dropdown">
                            <button type="button" wire:click="selectInvoice({{ $invoice->id }})" @click="open = false">{{ __('app.invoices.actions.open') }}</button>
                            @if ($invoice->status !== \App\InvoiceStatus::Paid)
                                <button type="button" wire:click="openPaymentModal({{ $invoice->id }})" @click="open = false">{{ __('app.invoices.actions.payments') }}</button>
                            @endif
                            <button type="button" wire:click="duplicateInvoiceFromList({{ $invoice->id }})" @click="open = false">{{ __('app.invoices.actions.create_copy') }}</button>
                            <button type="button" wire:click="openDeleteInvoiceModal({{ $invoice->id }})" @click="open = false" class="ek-invoice-menu-dropdown__danger">{{ __('app.invoices.actions.delete') }}</button>
                        </div>
                    </div>
                </div>
                <div class="ek-invoice-list-item__row ek-invoice-list-item__row--partner">
                    <span class="ek-invoice-list-item__partner">{{ $invoice->partner_name }}</span>
                </div>
                <div class="ek-invoice-list-item__row ek-invoice-list-item__row--meta">
                    @php $status = $invoice->status; @endphp
                    <div class="ek-invoice-list-item__status-group">
                        <span class="ek-invoice-status ek-invoice-status--{{ $status->value }}">{{ $status->label() }}</span>
                        @if ($invoice->emailed_at)
                            <span class="ek-invoice-emailed" title="{{ __('app.invoices.list.emailed_at', ['date' => $invoice->emailed_at->format('d.m.Y H:i')]) }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    <span class="ek-invoice-list-item__amount">{{ number_format((float) $invoice->total, 2, ',', ' ') }} €</span>
                </div>
            </div>
        @empty
            <div class="ek-invoice-list-empty">
                <p class="text-sm ek-text-secondary">{{ __('app.invoices.list.empty') }}</p>
            </div>
        @endforelse
    </div>

    <div class="ek-invoice-list-footer">
        <span>{{ trans_choice('app.invoices.documents_count', $filteredInvoices->count(), ['count' => $filteredInvoices->count()]) }}</span>
        <div class="ek-invoice-list-footer__totals">
            <span>{{ __('app.invoices.list.paid_total') }} <strong class="ek-invoice-list-footer__paid">{{ number_format((float) $paidSum, 2, ',', ' ') }} €</strong></span>
            <span>{{ __('app.invoices.list.unpaid_total') }} <strong class="ek-invoice-list-footer__unpaid">{{ number_format((float) $unpaidSum, 2, ',', ' ') }} €</strong></span>
        </div>
    </div>
</div>

@if ($showFilterModal)
    <div class="ek-modal-backdrop" wire:click="closeFilterModal">
        <div class="ek-modal ek-invoice-modal" wire:click.stop>
            <h3 class="text-lg font-semibold">{{ __('app.invoices.filter.title') }}</h3>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="filter-period" class="ek-label">{{ __('app.invoices.filter.period') }}</label>
                    <select wire:model.live="filterPeriod" id="filter-period" class="ek-input ek-select">
                        <option value="current_month">{{ __('app.invoices.filter.periods.current_month') }}</option>
                        <option value="last_month">{{ __('app.invoices.filter.periods.last_month') }}</option>
                        <option value="current_year">{{ __('app.invoices.filter.periods.current_year') }}</option>
                        <option value="last_year">{{ __('app.invoices.filter.periods.last_year') }}</option>
                        <option value="all">{{ __('app.invoices.filter.periods.all') }}</option>
                        <option value="custom">{{ __('app.invoices.filter.periods.custom') }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="filter-from" class="ek-label">{{ __('app.invoices.filter.date_from') }}</label>
                        <input wire:model="filterDateFrom" id="filter-from" type="date" class="ek-input" @disabled($filterPeriod !== 'custom')>
                    </div>
                    <div>
                        <label for="filter-to" class="ek-label">{{ __('app.invoices.filter.date_to') }}</label>
                        <input wire:model="filterDateTo" id="filter-to" type="date" class="ek-input" @disabled($filterPeriod !== 'custom')>
                    </div>
                </div>

                <div>
                    <label for="filter-partner-search" class="ek-label">{{ __('app.invoices.filter.partner') }}</label>
                    <input wire:model.live.debounce.300ms="filterPartnerSearch" id="filter-partner-search" type="search" class="ek-input mb-2" placeholder="{{ __('app.invoices.filter.partner_search') }}">
                    <select wire:model="filterPartnerIco" id="filter-partner" class="ek-input ek-select">
                        <option value="">{{ __('app.invoices.filter.all_partners') }}</option>
                        @foreach ($this->filteredPartnerOptions as $partner)
                            <option value="{{ $partner['ico'] }}">{{ $partner['name'] }}@if($partner['ico']) ({{ $partner['ico'] }})@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ek-modal-actions ek-modal-actions--split">
                <button type="button" wire:click="clearFilters" class="ek-link">{{ __('app.invoices.filter.clear') }}</button>
                <div class="flex gap-2">
                    <button type="button" wire:click="closeFilterModal" class="ek-btn-secondary" style="width: auto;">{{ __('app.invoices.filter.cancel') }}</button>
                    <button type="button" wire:click="applyFilters" class="ek-btn-primary" style="width: auto;">{{ __('app.invoices.filter.apply') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($showSortModal)
    <div class="ek-modal-backdrop" wire:click="closeSortModal">
        <div class="ek-modal ek-invoice-modal" wire:click.stop>
            <h3 class="text-lg font-semibold">{{ __('app.invoices.sort.title') }}</h3>

            <div class="mt-4 space-y-4">
                <div>
                    <p class="ek-label">{{ __('app.invoices.sort.field_label') }}</p>
                    <div class="ek-sort-options">
                        <label class="ek-sort-option"><input type="radio" wire:model="sortFieldDraft" value="number"> {{ __('app.invoices.sort.fields.number') }}</label>
                        <label class="ek-sort-option"><input type="radio" wire:model="sortFieldDraft" value="partner_name"> {{ __('app.invoices.sort.fields.partner_name') }}</label>
                        <label class="ek-sort-option"><input type="radio" wire:model="sortFieldDraft" value="total"> {{ __('app.invoices.sort.fields.total') }}</label>
                    </div>
                </div>

                <div>
                    <p class="ek-label">{{ __('app.invoices.sort.direction_label') }}</p>
                    <div class="ek-sort-options">
                        <label class="ek-sort-option"><input type="radio" wire:model="sortDirectionDraft" value="asc"> {{ __('app.invoices.sort.directions.asc') }}</label>
                        <label class="ek-sort-option"><input type="radio" wire:model="sortDirectionDraft" value="desc"> {{ __('app.invoices.sort.directions.desc') }}</label>
                    </div>
                </div>
            </div>

            <div class="ek-modal-actions">
                <button type="button" wire:click="closeSortModal" class="ek-btn-secondary" style="width: auto;">{{ __('app.invoices.sort.cancel') }}</button>
                <button type="button" wire:click="applySort" class="ek-btn-primary" style="width: auto;">{{ __('app.invoices.sort.apply') }}</button>
            </div>
        </div>
    </div>
@endif

@if ($showPaymentModal)
    <div class="ek-modal-backdrop" wire:click="closePaymentModal">
        <div class="ek-modal ek-invoice-modal" wire:click.stop>
            <h3 class="text-lg font-semibold">{{ __('app.invoices.payment.title') }}</h3>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="payment-paid-at" class="ek-label">{{ __('app.invoices.payment.date') }}</label>
                    <input wire:model="paymentForm.paid_at" id="payment-paid-at" type="date" class="ek-input">
                    @error('paymentForm.paid_at') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="payment-method" class="ek-label">{{ __('app.invoices.payment.method') }}</label>
                    <select wire:model="paymentForm.payment_method" id="payment-method" class="ek-input ek-select">
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->value }}">{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    @error('paymentForm.payment_method') <p class="ek-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="payment-amount" class="ek-label">{{ __('app.invoices.payment.amount') }}</label>
                    <input wire:model.live="paymentForm.amount" id="payment-amount" type="text" inputmode="decimal" class="ek-input">
                    @error('paymentForm.amount') <p class="ek-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="ek-modal-actions">
                <button type="button" wire:click="closePaymentModal" class="ek-btn-secondary" style="width: auto;">{{ __('app.invoices.payment.close') }}</button>
                <button type="button" wire:click="addPayment" class="ek-btn-primary" style="width: auto;">{{ __('app.invoices.payment.submit') }}</button>
            </div>
        </div>
    </div>
@endif

@if ($showDeleteInvoiceModal)
    <div class="ek-modal-backdrop" wire:click="closeDeleteInvoiceModal">
        <div class="ek-modal ek-invoice-modal" wire:click.stop>
            <h3 class="text-lg font-semibold">{{ __('app.invoices.delete.title') }}</h3>

            <p class="mt-3 text-sm ek-text-secondary">
                {{ __('app.invoices.delete.description') }}
            </p>

            <label class="mt-4 flex items-start gap-2 text-sm" style="color: var(--text);">
                <input wire:model.live="confirmDeleteInvoice" type="checkbox" class="ek-checkbox mt-0.5">
                <span>{{ __('app.invoices.delete.confirm') }}</span>
            </label>
            @error('confirmDeleteInvoice') <p class="ek-error mt-2">{{ $message }}</p> @enderror

            <div class="ek-modal-actions">
                <button type="button" wire:click="closeDeleteInvoiceModal" class="ek-btn-secondary" style="width: auto;">{{ __('app.invoices.delete.cancel') }}</button>
                <button
                    type="button"
                    wire:click="deleteInvoice"
                    wire:loading.attr="disabled"
                    wire:target="deleteInvoice"
                    class="ek-btn-secondary {{ $confirmDeleteInvoice ? '' : 'opacity-50' }}"
                    style="width: auto; color: var(--danger); border-color: color-mix(in srgb, var(--danger) 35%, var(--border2));"
                >
                    <span wire:loading.remove wire:target="deleteInvoice">{{ __('app.invoices.delete.submit') }}</span>
                    <span wire:loading wire:target="deleteInvoice">{{ __('app.invoices.delete.submitting') }}</span>
                </button>
            </div>
        </div>
    </div>
@endif
