# Invoices

**Invoices** — modern, self-hosted invoice management for freelancers and small businesses. Built with **Laravel 13** and **Livewire 4**, focused on the Slovak market (company registry lookup, PAY by square QR codes) while remaining fully usable in English.

![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## Features

### Invoicing
- Create, edit, duplicate and delete invoices
- Multiple line items with automatic totals
- Partner lookup via Slovak business registry (IČO search)
- Invoice statuses: paid, unpaid, overdue
- Filtering and sorting (period, partner, amount, number)
- PDF export with professional layout
- Email invoices with PDF attachment, PAY by square QR code and **per-email language** (SK/EN)
- Per-invoice or company-level logo and stamp/signature

### Company profiles
- Multiple company / sole-trader profiles per account
- Registry auto-fill from company name or IČO
- VAT payer types (non-VAT, VAT payer, identified person)
- Custom logo and stamp for each profile

### Authentication
- Email/password registration with **email verification**
- Password reset via email
- Google OAuth sign-in (verification skipped, welcome email sent)
- Resend verification with cooldown

### Backup
- **Google Drive backup** — automatic sync of profiles, invoices and PDFs
- Import from Drive (`backup.json` + assets)

### User experience
- Clean, responsive UI (desktop, tablet, mobile)
- Dark / light theme
- **Slovak & English** UI (flag switch in header)
- Mobile burger menu and fullscreen invoice editor

## Screenshots

> Add screenshots before publishing.

## Requirements

- PHP 8.3+
- Composer 2
- Node.js 18+ & npm (for Vite assets)
- SQLite, MySQL/MariaDB or PostgreSQL
- Optional: `xz` binary for PAY by square QR in PDF/email (Git for Windows includes it)

## Quick start

```bash
git clone https://github.com/Revolta77/Invoices.git
cd Invoices
composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite
touch database/database.sqlite
php artisan migrate

npm run build
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000), register and create your first company profile.

### Queue worker (Google Drive backup)

```bash
php artisan queue:work
```

### Local email testing (Mailpit)

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

## Configuration

### Language

```env
APP_LOCALE=sk
APP_FALLBACK_LOCALE=en
APP_AVAILABLE_LOCALES=sk,en
APP_NAME=Faktury
```

### Email verification

```env
AUTH_VERIFICATION_EXPIRE=5
AUTH_VERIFICATION_RESEND_THROTTLE=60
```

### Google OAuth (login + Drive)

1. Create a project in [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google Drive API
3. Create OAuth 2.0 credentials (Web application)
4. Authorized redirect URIs:
   - `{APP_URL}/auth/google/callback`
   - `{APP_URL}/auth/google/link/callback`

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

GOOGLE_DRIVE_BACKUP_ENABLED=true
GOOGLE_DRIVE_ROOT_FOLDER=Faktury
```

### PAY by square (Windows)

```env
XZ_BINARY_PATH="C:\Program Files\Git\mingw64\bin\xz.exe"
```

## Project structure

```
app/
  Livewire/           # UI (AppShell, invoices, settings, auth)
  Services/           # PDF, Google Drive backup, Subjekt API
  Support/            # Invoice preview, PAY by square, helpers
resources/views/overrides/   # Custom Blade templates (priority)
lang/sk/app.php, lang/en/app.php
public/css/envkit-theme.css
```

## Tech stack

| Layer     | Technology |
|-----------|------------|
| Backend   | Laravel 13 |
| Frontend  | Livewire 4, Blade |
| PDF       | DomPDF |
| Auth      | Laravel + Socialite (Google) |
| Styling   | Custom CSS + Tailwind (Vite) |

## Development

```bash
composer run dev
php artisan test
./vendor/bin/pint
```

## Roadmap

- [ ] PEPPOL / mandatory e-invoicing (when a suitable API is available)
- [ ] PDF language per invoice email
- [ ] Recurring invoices

## License

[MIT](LICENSE)

---

**Slovensky:** **Faktury** je open-source aplikácia na správu faktúr pre živnostníkov a malé firmy na Slovensku. Podporuje slovenčinu aj angličtinu, PAY by square, PDF, e-mail, overenie účtu, obnovu hesla a zálohu na Google Drive.
