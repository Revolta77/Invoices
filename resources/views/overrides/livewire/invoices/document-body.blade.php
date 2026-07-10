@php
    $interactiveQr = $interactiveQr ?? false;
@endphp

<article class="ek-invoice-preview-doc">
    <header class="ek-invoice-preview-doc__header">
        <div class="ek-invoice-preview-doc__logo">
            @if ($preview['supplier']['logo_url'])
                <img src="{{ $preview['supplier']['logo_url'] }}" alt="{{ __('app.document.logo_alt') }}">
            @endif
        </div>
        <p class="ek-invoice-preview-doc__title-line">
            {{ __('app.document.title', ['number' => $preview['number'] ?: __('app.dash')]) }}
        </p>
    </header>

    <div class="ek-invoice-preview-doc__parties">
        <section class="ek-invoice-preview-doc__party">
            <h4 class="ek-invoice-preview-doc__heading">{{ __('app.document.supplier') }}</h4>
            <p class="ek-invoice-preview-doc__name">{{ $preview['supplier']['name'] ?: __('app.dash') }}</p>
            @if ($preview['supplier']['street'])
                <p>{{ $preview['supplier']['street'] }}</p>
            @endif
            @if ($preview['supplier']['postal_code'] || $preview['supplier']['city'])
                <p>{{ trim(($preview['supplier']['postal_code'] ?? '').' '.($preview['supplier']['city'] ?? '')) }}</p>
            @endif
            @if ($preview['supplier']['ico'])
                <p>{{ __('app.document.ico') }} {{ $preview['supplier']['ico'] }}</p>
            @endif
            @if ($preview['supplier']['dic'])
                <p>{{ __('app.document.dic') }} {{ $preview['supplier']['dic'] }}</p>
            @endif
            @if ($preview['supplier']['ic_dph'])
                <p>{{ __('app.document.ic_dph') }} {{ $preview['supplier']['ic_dph'] }}</p>
            @endif
            @if ($preview['supplier']['registry'])
                <p>{{ $preview['supplier']['registry'] }}</p>
            @endif
        </section>

        <section class="ek-invoice-preview-doc__party">
            <h4 class="ek-invoice-preview-doc__heading">{{ __('app.document.customer') }}</h4>
            <p class="ek-invoice-preview-doc__name">{{ $preview['customer']['name'] ?: __('app.dash') }}</p>
            @if ($preview['customer']['street'])
                <p>{{ $preview['customer']['street'] }}</p>
            @endif
            @if ($preview['customer']['postal_code'] || $preview['customer']['city'])
                <p>{{ trim(($preview['customer']['postal_code'] ?? '').' '.($preview['customer']['city'] ?? '')) }}</p>
            @endif
            @if ($preview['customer']['ico'])
                <p>{{ __('app.document.ico') }} {{ $preview['customer']['ico'] }}</p>
            @endif
            @if ($preview['customer']['dic'])
                <p>{{ __('app.document.dic') }} {{ $preview['customer']['dic'] }}</p>
            @endif
            @if ($preview['customer']['ic_dph'])
                <p>{{ __('app.document.ic_dph') }} {{ $preview['customer']['ic_dph'] }}</p>
            @endif
        </section>
    </div>

    <div class="ek-invoice-preview-doc__meta-row">
        <div class="ek-invoice-preview-doc__meta-left">
            @if ($preview['supplier']['email'] || $preview['supplier']['phone'] || $preview['supplier']['web'])
                <h4 class="ek-invoice-preview-doc__heading">{{ __('app.document.contact_details') }}</h4>
                @if ($preview['supplier']['email'])
                    <p>{{ $preview['supplier']['email'] }}</p>
                @endif
                @if ($preview['supplier']['phone'])
                    <p>{{ $preview['supplier']['phone'] }}</p>
                @endif
                @if ($preview['supplier']['web'])
                    <p>{{ $preview['supplier']['web'] }}</p>
                @endif
            @endif

            <div class="ek-invoice-preview-doc__dates">
                <p><span>{{ __('app.document.issue_date') }}</span> {{ $preview['issue_date'] ?? __('app.dash') }}</p>
                <p><span>{{ __('app.document.delivery_date') }}</span> {{ $preview['delivery_date'] ?? __('app.dash') }}</p>
                <p><span>{{ __('app.document.due_date') }}</span> {{ $preview['due_date'] ?? __('app.dash') }}</p>
            </div>
        </div>

        <div @class([
            'ek-invoice-preview-doc__payment-box',
            'ek-invoice-preview-doc__payment-box--with-qr' => $preview['is_bank_transfer'] && filled($preview['iban']),
        ])>
            <div class="ek-invoice-preview-doc__payment-details">
                <p class="ek-invoice-preview-doc__payment-label">{{ __('app.document.amount') }}</p>
                <p class="ek-invoice-preview-doc__payment-amount">
                    {{ number_format($preview['total'], 2, ',', ' ') }} {{ $preview['currency'] }}
                </p>
                <div class="ek-invoice-preview-doc__payment-divider"></div>
                <p><span>{{ __('app.document.payment_method') }}</span> {{ $preview['payment_method'] }}</p>
                @if ($preview['is_bank_transfer'])
                    <p><span>{{ __('app.document.variable_symbol') }}</span> {{ $preview['number'] ?: __('app.dash') }}</p>
                    @if ($preview['iban'])
                        <p class="ek-invoice-preview-doc__iban-line"><span>{{ __('app.document.iban') }}</span> {{ preg_replace('/\s+/', '', $preview['iban']) }}</p>
                    @endif
                    @if ($preview['swift'])
                        <p><span>{{ __('app.document.swift') }}</span> {{ $preview['swift'] }}</p>
                    @endif
                @endif
            </div>

            @if ($preview['is_bank_transfer'] && filled($preview['iban']))
                <div class="ek-invoice-preview-doc__qr">
                    @if ($interactiveQr)
                        <div
                            wire:ignore
                            x-data="{
                                loading: true,
                                error: false,
                                async init() {
                                    const canvas = this.$refs.qrCanvas;
                                    const params = @js($preview['pay_by_square_params']);

                                    try {
                                        await this.loadScript(@js(asset('js/invoice-pay-by-square.js')));
                                        await window.InvoicePayBySquare.renderToCanvas(canvas, params);
                                    } catch (e) {
                                        console.error('PAY by square QR error', e);
                                        this.error = true;
                                    } finally {
                                        this.loading = false;
                                    }
                                },
                                loadScript(src) {
                                    if (document.querySelector(`script[src='${src}']`)) {
                                        return Promise.resolve();
                                    }

                                    return new Promise((resolve, reject) => {
                                        const script = document.createElement('script');
                                        script.src = src;
                                        script.onload = resolve;
                                        script.onerror = reject;
                                        document.head.appendChild(script);
                                    });
                                }
                            }"
                        >
                            <div class="ek-invoice-preview-doc__qr-frame" :class="{ 'ek-invoice-preview-doc__qr-frame--loading': loading }">
                                <canvas x-show="!error" x-ref="qrCanvas" width="140" height="140" aria-label="{{ __('app.document.qr_alt') }}"></canvas>
                                <p x-show="loading && !error" class="ek-invoice-preview-doc__qr-status">{{ __('app.document.qr_generating') }}</p>
                                <p x-show="error" x-cloak class="ek-invoice-preview-doc__qr-status ek-invoice-preview-doc__qr-status--error">{{ __('app.document.qr_error') }}</p>
                            </div>
                        </div>
                    @elseif (! empty($preview['pay_by_square_qr_url']))
                        <div class="ek-invoice-preview-doc__qr-frame">
                            <img src="{{ $preview['pay_by_square_qr_url'] }}" width="140" height="140" alt="{{ __('app.document.qr_alt') }}">
                        </div>
                    @endif
                    <p class="ek-invoice-preview-doc__qr-label">{{ __('app.document.pay_by_square') }}</p>
                </div>
            @endif
        </div>
    </div>

    @if ($preview['is_identified_person'])
        <p class="ek-invoice-preview-doc__note">{{ __('app.document.identified_person_note') }}</p>
    @endif

    <table class="ek-invoice-preview-doc__table">
        <thead>
            <tr>
                <th>{{ __('app.document.table.position') }}</th>
                <th>{{ __('app.document.table.name') }}</th>
                <th>{{ __('app.document.table.quantity') }}</th>
                <th>{{ __('app.document.table.unit') }}</th>
                <th>{{ __('app.document.table.price') }}</th>
                <th>{{ __('app.document.table.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($preview['items'] as $item)
                <tr>
                    <td>{{ $item['position'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['quantity'], 3, ',', ' ') }}</td>
                    <td>{{ $item['unit'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2, ',', ' ') }}</td>
                    <td>{{ number_format($item['total'], 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="ek-invoice-preview-doc__empty">{{ __('app.document.table.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ek-invoice-preview-doc__bottom">
        <div class="ek-invoice-preview-doc__total-box">
            <span>{{ __('app.document.grand_total') }}</span>
            <strong>{{ number_format($preview['total'], 2, ',', ' ') }} {{ $preview['currency'] }}</strong>
        </div>

        @if ($preview['stamp_url'])
            <div class="ek-invoice-preview-doc__stamp-block">
                <img src="{{ $preview['stamp_url'] }}" alt="{{ __('app.document.stamp_alt') }}">
                <div class="ek-invoice-preview-doc__stamp-line"></div>
                <p>{{ __('app.document.stamp_label') }}</p>
            </div>
        @endif
    </div>
</article>
