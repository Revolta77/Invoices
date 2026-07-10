<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.document.title', ['number' => $preview['number']]) }}</title>
    <style>
        @page { margin: 7mm 6mm; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.55;
            color: #111827;
        }

        .doc { width: 100%; }

        p.heading {
            margin: 0 0 8px;
            font-size: 16.6px;
            font-weight: 700;
            color: #0c7a61;
        }

        p.name {
            font-weight: 700;
            color: #111827;
            margin: 0 0 2px;
        }

        p.body-text {
            margin: 0 0 2px;
            color: #374151;
        }

        .header-table { width: 100%; margin-bottom: 24px; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .logo img { display: block; max-height: 72px; max-width: 160px; }
        .title {
            text-align: right;
            font-size: 22.8px;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
        }

        .parties { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .parties td { width: 50%; vertical-align: top; padding-right: 32px; }

        .meta-table { width: 100%; margin-bottom: 24px; border-collapse: collapse; }
        .meta-table > tbody > tr > td { vertical-align: top; }
        .meta-left { width: 38%; padding-right: 20px; }
        .meta-right { width: 62%; }

        .meta-left p.body-text { color: #374151; }
        .dates { margin-top: 8px; }
        .dates p { color: #111827; margin: 3px 0; }
        .dates span { color: #374151; }

        .payment-outer {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #e2efec;
            border: 1px solid #bed3d2;
            border-radius: 6px;
        }

        .payment-outer > tbody > tr > td { padding: 16px 17px; vertical-align: top; }

        .payment-inner { width: 100%; border-collapse: collapse; }
        .payment-inner td { padding: 0; }
        .payment-details { padding-right: 16px; vertical-align: middle; }
        .payment-details p.detail-line { margin: 5px 0 0; color: #111827; }
        .payment-details p.detail-line span { color: #374151; }

        p.payment-label {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #111827;
        }

        p.payment-amount {
            margin: 0 0 7px;
            font-size: 20px;
            font-weight: 700;
            color: #0c7a61;
        }

        .payment-divider {
            height: 1px;
            margin: 10px 0;
            background: #a0bebd;
            line-height: 0;
            font-size: 0;
        }

        p.iban-line {
            white-space: nowrap;
            font-size: 11.5px;
            letter-spacing: 0.005em;
            margin: 5px 0 0;
            color: #111827;
        }

        p.iban-line span { color: #374151; }

        .qr-cell {
            width: 168px;
            vertical-align: middle;
            padding-left: 8px;
        }

        .qr-wrap { text-align: center; padding: 4px 0; }

        .qr-frame {
            width: 126px;
            height: 126px;
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #c4d7d6;
            border-radius: 4px;
            background: #fff;
            text-align: center;
        }

        .qr-frame img {
            width: 124px;
            height: 124px;
            display: block;
            margin: 0 auto;
        }

        p.qr-label {
            margin: 6px 0 0;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0c7a61;
            text-align: center;
        }

        .note {
            margin: 0 0 16px;
            font-size: 12px;
            color: #374151;
        }

        .items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items thead th {
            background: #0c7a61;
            color: #fff;
            padding: 9px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: left;
            border: none;
        }

        .items thead th:first-child { border-radius: 4px 0 0 0; }
        .items thead th:last-child { border-radius: 0 4px 0 0; }

        .items tbody td {
            padding: 9px 8px;
            vertical-align: top;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
        }

        .items tbody tr.even td { background: #f3f4f6; }
        .items td.num, .items th.num { text-align: right; white-space: nowrap; }
        .empty { text-align: center; color: #6b7280; font-style: italic; }

        .bottom-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .bottom-table td { vertical-align: bottom; }
        .bottom-left { width: 70%; padding-right: 24px; }

        .total-box {
            width: 100%;
            padding: 14px 16px;
            background: #e2efec;
            border: 1px solid #bed3d2;
            border-radius: 6px;
        }

        .total-box span {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0c7a61;
        }

        .total-box strong {
            display: block;
            margin-top: 4px;
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        .bottom-right { width: 30%; text-align: center; }

        .stamp-block { display: inline-block; text-align: center; min-width: 128px; }
        .stamp-block img { max-height: 72px; max-width: 112px; }
        .stamp-line { height: 1px; margin: 8px 0 6px; background: #9ca3af; }
        .stamp-block p { margin: 0; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="doc">
        <table class="header-table">
            <tr>
                <td class="logo">
                    @if (! empty($preview['supplier']['logo_url']))
                        <img src="{{ $preview['supplier']['logo_url'] }}" alt="Logo">
                    @endif
                </td>
                <td class="title">{{ __('app.document.title', ['number' => $preview['number'] ?: __('app.dash')]) }}</td>
            </tr>
        </table>

        <table class="parties">
            <tr>
                <td>
                    <p class="heading">{{ __('app.document.supplier') }}</p>
                    <p class="name">{{ $preview['supplier']['name'] ?: '—' }}</p>
                    @if ($preview['supplier']['street'])<p class="body-text">{{ $preview['supplier']['street'] }}</p>@endif
                    @if ($preview['supplier']['postal_code'] || $preview['supplier']['city'])
                        <p class="body-text">{{ trim(($preview['supplier']['postal_code'] ?? '').' '.($preview['supplier']['city'] ?? '')) }}</p>
                    @endif
                    @if ($preview['supplier']['ico'])<p class="body-text">{{ __('app.document.ico') }} {{ $preview['supplier']['ico'] }}</p>@endif
                    @if ($preview['supplier']['dic'])<p class="body-text">{{ __('app.document.dic') }} {{ $preview['supplier']['dic'] }}</p>@endif
                    @if ($preview['supplier']['ic_dph'])<p class="body-text">{{ __('app.document.ic_dph') }} {{ $preview['supplier']['ic_dph'] }}</p>@endif
                    @if ($preview['supplier']['registry'])<p class="body-text">{{ $preview['supplier']['registry'] }}</p>@endif
                </td>
                <td>
                    <p class="heading">{{ __('app.document.customer') }}</p>
                    <p class="name">{{ $preview['customer']['name'] ?: '—' }}</p>
                    @if ($preview['customer']['street'])<p class="body-text">{{ $preview['customer']['street'] }}</p>@endif
                    @if ($preview['customer']['postal_code'] || $preview['customer']['city'])
                        <p class="body-text">{{ trim(($preview['customer']['postal_code'] ?? '').' '.($preview['customer']['city'] ?? '')) }}</p>
                    @endif
                    @if ($preview['customer']['ico'])<p class="body-text">{{ __('app.document.ico') }} {{ $preview['customer']['ico'] }}</p>@endif
                    @if ($preview['customer']['dic'])<p class="body-text">{{ __('app.document.dic') }} {{ $preview['customer']['dic'] }}</p>@endif
                    @if ($preview['customer']['ic_dph'])<p class="body-text">{{ __('app.document.ic_dph') }} {{ $preview['customer']['ic_dph'] }}</p>@endif
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td class="meta-left">
                    @if ($preview['supplier']['email'] || $preview['supplier']['phone'] || $preview['supplier']['web'])
                        <p class="heading">{{ __('app.document.contact_details') }}</p>
                        @if ($preview['supplier']['email'])<p class="body-text">{{ $preview['supplier']['email'] }}</p>@endif
                        @if ($preview['supplier']['phone'])<p class="body-text">{{ $preview['supplier']['phone'] }}</p>@endif
                        @if ($preview['supplier']['web'])<p class="body-text">{{ $preview['supplier']['web'] }}</p>@endif
                    @endif
                    <div class="dates">
                        <p><span>{{ __('app.document.issue_date') }}</span> {{ $preview['issue_date'] ?? __('app.dash') }}</p>
                        <p><span>{{ __('app.document.delivery_date') }}</span> {{ $preview['delivery_date'] ?? __('app.dash') }}</p>
                        <p><span>{{ __('app.document.due_date') }}</span> {{ $preview['due_date'] ?? __('app.dash') }}</p>
                    </div>
                </td>
                <td class="meta-right">
                    <table class="payment-outer">
                        <tr>
                            <td>
                                <table class="payment-inner">
                                    <tr>
                                        <td class="payment-details">
                                            <p class="payment-label">{{ __('app.document.amount') }}</p>
                                            <p class="payment-amount">{{ number_format($preview['total'], 2, ',', ' ') }} {{ $preview['currency'] }}</p>
                                            <div class="payment-divider">&nbsp;</div>
                                            <p class="detail-line"><span>{{ __('app.document.payment_method') }}</span> {{ $preview['payment_method'] }}</p>
                                            @if ($preview['is_bank_transfer'])
                                                <p class="detail-line"><span>{{ __('app.document.variable_symbol') }}</span> {{ $preview['number'] ?: __('app.dash') }}</p>
                                                @if ($preview['iban'])
                                                    <p class="iban-line"><span>{{ __('app.document.iban') }}</span> {{ preg_replace('/\s+/', '', $preview['iban']) }}</p>
                                                @endif
                                                @if ($preview['swift'])
                                                    <p class="detail-line"><span>{{ __('app.document.swift') }}</span> {{ $preview['swift'] }}</p>
                                                @endif
                                            @endif
                                        </td>
                                        @if ($preview['is_bank_transfer'] && filled($preview['iban']) && ! empty($preview['pay_by_square_qr_url']))
                                            <td class="qr-cell">
                                                <div class="qr-wrap">
                                                    <div class="qr-frame">
                                                        <img src="{{ $preview['pay_by_square_qr_url'] }}" alt="{{ __('app.document.qr_alt') }}">
                                                    </div>
                                                    <p class="qr-label">{{ __('app.document.pay_by_square') }}</p>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if ($preview['is_identified_person'])
            <p class="note">{{ __('app.document.identified_person_note') }}</p>
        @endif

        <table class="items">
            <thead>
                <tr>
                    <th style="width:28px;">{{ __('app.document.table.position') }}</th>
                    <th>{{ __('app.document.table.name') }}</th>
                    <th class="num" style="width:70px;">{{ __('app.document.table.quantity') }}</th>
                    <th style="width:40px;">{{ __('app.document.table.unit') }}</th>
                    <th class="num" style="width:70px;">{{ __('app.document.table.price') }}</th>
                    <th class="num" style="width:80px;">{{ __('app.document.table.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview['items'] as $item)
                    <tr @class(['even' => $loop->iteration % 2 === 0])>
                        <td>{{ $item['position'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="num">{{ number_format($item['quantity'], 3, ',', ' ') }}</td>
                        <td>{{ $item['unit'] }}</td>
                        <td class="num">{{ number_format($item['unit_price'], 2, ',', ' ') }}</td>
                        <td class="num">{{ number_format($item['total'], 2, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">{{ __('app.document.table.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="bottom-table">
            <tr>
                <td class="bottom-left">
                    <div class="total-box">
                        <span>{{ __('app.document.grand_total') }}</span>
                        <strong>{{ number_format($preview['total'], 2, ',', ' ') }} {{ $preview['currency'] }}</strong>
                    </div>
                </td>
                <td class="bottom-right">
                    @if ($preview['stamp_url'])
                        <div class="stamp-block">
                            <img src="{{ $preview['stamp_url'] }}" alt="{{ __('app.document.stamp_alt') }}">
                            <div class="stamp-line"></div>
                            <p>{{ __('app.document.stamp_label') }}</p>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
