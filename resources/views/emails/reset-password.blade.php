<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.emails.reset_password.subject', ['app' => config('app.name')]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f6f7;font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;color:#18181b;line-height:1.55;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f6f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px 12px;">
                            <p style="margin:0 0 8px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#0c7a61;">
                                {{ config('app.name') }}
                            </p>
                            <h1 style="margin:0;font-size:24px;font-weight:700;color:#111827;">
                                {{ __('app.emails.reset_password.heading') }}
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 0;font-size:15px;color:#374151;">
                            <p style="margin:0 0 16px;">{{ __('app.emails.reset_password.intro', ['name' => $user->name]) }}</p>
                            <p style="margin:0 0 16px;">{{ __('app.emails.reset_password.body') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="border-radius:10px;background:#0c7a61;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block;padding:14px 24px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            {{ __('app.emails.reset_password.button') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px 0;font-size:13px;color:#6b7280;">
                            <p style="margin:0 0 8px;">{{ __('app.emails.reset_password.expires', ['minutes' => config('auth.passwords.users.expire', 60)]) }}</p>
                            <p style="margin:0;word-break:break-all;">{{ $resetUrl }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 28px;font-size:13px;color:#6b7280;">
                            <p style="margin:0;">{{ __('app.emails.reset_password.footer') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
