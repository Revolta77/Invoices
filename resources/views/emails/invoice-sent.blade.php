<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $paymentDetails['subject'] ?? __('app.emails.invoice_sent.heading', ['number' => $paymentDetails['number'] ?? '']) }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f6f7;font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;color:#18181b;line-height:1.55;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f6f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px 12px;">
                            <p style="margin:0 0 8px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#0c7a61;">
                                {{ $paymentDetails['from_name'] ?? config('app.name') }}
                            </p>
                            <h1 style="margin:0;font-size:24px;font-weight:700;color:#111827;">
                                {{ __('app.emails.invoice_sent.heading', ['number' => $paymentDetails['number'] ?? '']) }}
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px 32px 0;font-size:15px;color:#374151;">
                            <div style="margin:0;white-space:pre-line;">{{ $messageBody }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 32px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f0fdf9;border:1px solid #99f6e4;border-radius:10px;padding:18px 20px;">
                                <tr>
                                    <td style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#0c7a61;padding-bottom:12px;">
                                        {{ __('app.emails.invoice_sent.payment_section') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;color:#374151;">
                                        <p style="margin:0 0 8px;">
                                            <strong>{{ __('app.emails.invoice_sent.amount') }}:</strong>
                                            {{ $paymentDetails['total_formatted'] }}
                                        </p>
                                        <p style="margin:0 0 8px;">
                                            <strong>{{ __('app.emails.invoice_sent.payment_method') }}:</strong>
                                            {{ $paymentDetails['payment_method'] }}
                                        </p>
                                        @if (! empty($paymentDetails['is_bank_transfer']))
                                            <p style="margin:0 0 8px;">
                                                <strong>{{ __('app.emails.invoice_sent.variable_symbol') }}:</strong>
                                                {{ $paymentDetails['number'] }}
                                            </p>
                                            @if (! empty($paymentDetails['iban']))
                                                <p style="margin:0 0 8px;">
                                                    <strong>{{ __('app.emails.invoice_sent.iban') }}:</strong>
                                                    {{ $paymentDetails['iban'] }}
                                                </p>
                                            @endif
                                            @if (! empty($paymentDetails['swift']))
                                                <p style="margin:0 0 8px;">
                                                    <strong>{{ __('app.emails.invoice_sent.swift') }}:</strong>
                                                    {{ $paymentDetails['swift'] }}
                                                </p>
                                            @endif
                                            @if (! empty($paymentDetails['due_date']))
                                                <p style="margin:0;">
                                                    <strong>{{ __('app.emails.invoice_sent.due_date') }}:</strong>
                                                    {{ $paymentDetails['due_date'] }}
                                                </p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @if (! empty($paymentDetails['pay_by_square_qr_url']))
                                    <tr>
                                        <td style="padding-top:18px;text-align:center;">
                                            <p style="margin:0 0 10px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#0c7a61;">
                                                {{ __('app.emails.invoice_sent.pay_by_square') }}
                                            </p>
                                            <img
                                                src="{{ $paymentDetails['pay_by_square_qr_url'] }}"
                                                alt="{{ __('app.emails.invoice_sent.qr_alt') }}"
                                                width="140"
                                                height="140"
                                                style="display:inline-block;border:1px solid #e5e7eb;border-radius:8px;background:#ffffff;"
                                            >
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 0;font-size:13px;color:#6b7280;">
                            <p style="margin:0;">{{ __('app.emails.invoice_sent.attachment_hint') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 32px 28px;font-size:13px;color:#9ca3af;">
                            <p style="margin:0;">{{ __('app.emails.invoice_sent.footer', ['app' => config('app.name')]) }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
