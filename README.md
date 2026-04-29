# BKGS — School Teacher Fee Collection System

A Laravel 13 + Livewire 3 web app for a Malaysian school where teachers log in to view and pay their monthly fees online via **BayarCash** (FPX/DuitNow) and receive **WhatsApp notifications via Sendora**. Includes an admin dashboard for managing teachers, fee structures, invoices, payments, branding, and reports.

## Tech Stack

- Laravel 13, PHP 8.3
- Livewire 3 (class-based components) + Tailwind CSS via Vite + Alpine.js
- MySQL 8 (or MariaDB 10.6+)
- Laravel Breeze (Livewire stack) for auth scaffolding
- `spatie/laravel-permission` (`teacher`, `admin`, `super-admin` roles)
- `webimpian/bayarcash-php-sdk` (FPX / DuitNow / etc.)
- `intervention/image` (logo resize, square pad)
- `barryvdh/laravel-dompdf` (PDF receipts)
- `maatwebsite/excel` (monthly collection report)
- Custom HTTP client wrapping the Sendora WhatsApp API

## Quick Start (development)

```bash
git clone https://github.com/chillocreative/bkgs.git
cd bkgs
composer install
npm install
cp .env.example .env       # then fill in values (see "Environment" below)
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve --port=8000
```

Default super-admin (created by `SuperAdminSeeder`):

| Email                | Password   |
|----------------------|------------|
| `super@school.test`  | `password` |

Change the password immediately after first login (`/profile`).

## Environment

The full `.env.example` lives in the repo. Required keys grouped by feature:

```ini
# Database (Laragon defaults)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bkgs
DB_USERNAME=root
DB_PASSWORD=

# Queue + cache backends (database is fine for ≤40 users)
QUEUE_CONNECTION=database
CACHE_STORE=database

# BayarCash — leave blank in .env if you use the in-app super-admin Settings page
# (super-admin > Settings > BayarCash). Settings stored there are encrypted at rest.
BAYARCASH_API_TOKEN=
BAYARCASH_API_SECRET_KEY=
BAYARCASH_PORTAL_KEY=
BAYARCASH_SANDBOX=true
BAYARCASH_API_VERSION=v3

# Sendora — same: configure via super-admin > Settings > Sendora.
SENDORA_API_KEY=
SENDORA_BASE_URL=https://sendora.cc/api/v1
SENDORA_DEVICE_ID=
SENDORA_TIMEOUT=10
```

App-stored settings (DB) override `.env` values for both BayarCash and Sendora — that's intentional so credentials can be rotated without redeploys.

## Roles & first-run

1. Log in as super-admin → `/profile` → set a real password.
2. **Settings → General**: set school name, address, contact info, receipt footer.
3. **Settings → Branding**: upload your school logo (200×200 to 2000×2000, PNG/JPG/WEBP/SVG ≤ 2 MB). Two derivative sizes (256, 512) are generated automatically and applied across the login page, header, and PDF receipts.
4. **Settings → BayarCash**: paste API token + secret key + portal key (sandbox first).
5. **Settings → Sendora**: paste API key, choose device, send a test message.
6. **Fee Structures**: create at least one (mark it default).
7. **Teachers**: create one by hand or use **Bulk Import** with a CSV (`name,email,phone,ic_number,monthly_fee_amount`).
8. **Invoices → Generate Monthly**: idempotent. Run on day 1 of the month or schedule the cron.

## BayarCash sandbox setup

1. Sign up for BayarCash; you'll get sandbox API token, secret, and portal key.
2. Register these URLs in your BayarCash portal:
   - **Return URL**: `https://your-domain/payment/return`
   - **Callback URL**: `https://your-domain/webhooks/bayarcash/callback`
3. In the app: super-admin → **Settings → BayarCash** → paste credentials, **Mode = Sandbox**.
4. From a teacher account: open an invoice, click **Pay Now**, finish FPX/DuitNow sandbox flow → back to `/payment/return` → invoice flips to `paid` → WhatsApp confirmation queued.

The callback handler is keyed on `bayarcash_transaction_id` (unique index on `payments`); replays are safe and won't duplicate the row.

## Sendora WhatsApp setup

Sendora exposes a REST API documented internally; this build targets:

```
POST {SENDORA_BASE_URL}/messages/send
Authorization: Bearer {api_key}
{ "phone": "60XXXXXXXXX", "message": "...", "device_id": optional }
```

Phone numbers are normalised by `App\Support\PhoneFormatter` — `0123456789`, `+60123456789`, and `60123456789` all become `60123456789`. Garbage rejects with `InvalidArgumentException`.

The five built-in templates live in the `whatsapp_templates` table (admin can edit copy without code changes):

| Key                | When fired                                    |
|--------------------|-----------------------------------------------|
| `invoice_issued`   | After monthly invoice generation              |
| `payment_received` | On successful BayarCash callback or manual record |
| `payment_reminder` | 3 days before due date (scheduled job)        |
| `payment_overdue`  | Day after due date (scheduled job)            |
| `manual_reminder`  | Admin clicks **Send Reminder** on an invoice  |

Available variables: `{{ teacher_name }}`, `{{ school_name }}`, `{{ amount }}`, `{{ invoice_number }}`, `{{ month }}`, `{{ due_date }}`, `{{ paid_at }}`, `{{ pay_url }}`. Every send is logged in `notification_logs` (admin → **Notifications**).

## Scheduled jobs

This project ships these scheduled commands (in `routes/console.php`):

| Command                             | Cadence              |
|-------------------------------------|----------------------|
| `invoices:generate-monthly`         | 1st of each month, 06:00 |
| `invoices:send-reminders --days=3`  | Daily, 09:00         |
| `invoices:send-overdue`             | Daily, 09:30         |

Add to your crontab on the server:

```
* * * * * cd /var/www/bkgs && php artisan schedule:run >> /dev/null 2>&1
```

A queue worker is also required (notifications are queued):

```
php artisan queue:work --queue=default --tries=3 --backoff=30,120,600
```

In production use Supervisor; locally just run it in another terminal.

## Reports & PDFs

- `Admin → Reports → Export Monthly` (or the button on the Payments index) downloads `collection-YYYY-MM.xlsx` via `maatwebsite/excel`.
- `Pay → Receipt` generates a DomPDF receipt, embedding the school logo and details.

## Test suite

```bash
php artisan test
```

Currently green: 40 tests, 126 assertions. The four highlight tests:

| File                                       | What it asserts |
|--------------------------------------------|-----------------|
| `tests/Feature/InvoiceGenerationTest.php`  | One invoice per active teacher · idempotent on second run · per-teacher amount overrides default |
| `tests/Feature/BayarcashCallbackIdempotencyTest.php` | Replayed BayarCash callback does not duplicate the payment row · invoice flips to paid |
| `tests/Feature/SendoraSendTest.php`        | HTTP request shape + auth header + phone normalisation; failure mode for 422 |
| `tests/Feature/LogoUploadTest.php`         | Branding page generates 256/512 derivatives and stores paths in `settings` |

Plus a `SmokeRoutesTest` walking every admin and teacher route as super-admin and asserting 200.

## Deployment notes (DigitalOcean SG / Exabytes / cPanel)

1. PHP 8.3, Composer 2, Node 20+, MySQL 8.
2. `composer install --optimize-autoloader --no-dev`
3. `npm ci && npm run build`
4. `cp .env.example .env`, fill in DB and `APP_*`, `php artisan key:generate`.
5. `php artisan storage:link` (logo uploads land in `storage/app/public/branding/...` and are served via the symlink).
6. `php artisan migrate --force --seed`
7. Webserver: point document root at `public/`. `.htaccess` (Apache) is shipped by Laravel default.
8. Cron entry above.
9. Supervisor / pm2 entry to keep `queue:work` alive.

## Project structure

```
app/
├── Console/Commands/      # generate-monthly, send-reminders, send-overdue
├── Enums/                 # Invoice/Payment/Notification status enums
├── Exports/               # MonthlyCollectionExport (Excel)
├── Http/Controllers/
│   ├── ReceiptController  # DomPDF receipt download
│   ├── ReportController   # Excel report
│   └── Webhooks/          # BayarcashController, SendoraController
├── Livewire/
│   ├── Admin/             # Dashboard + Teachers + FeeStructures + Invoices + Payments + Settings + Notifications
│   ├── Teacher/           # InvoicesIndex, InvoiceShow
│   ├── PayInvoice         # FPX redirect
│   └── PaymentReturn      # post-BayarCash landing
├── Models/                # User, Invoice, Payment, FeeStructure, NotificationLog, WhatsappTemplate, Setting, AuditLog
├── Notifications/
│   ├── Channels/SendoraChannel
│   ├── Concerns/BuildsSendoraMessage
│   └── (5 notification classes)
├── Observers/AuditObserver
├── Services/              # BayarcashService, SendoraService, InvoiceGenerator, LogoProcessor
├── Support/               # PhoneFormatter, TemplateRenderer
└── View/Components/AppLogo
database/
├── migrations/            # 7 app migrations + Spatie + Breeze defaults
└── seeders/               # Role + SuperAdmin + WhatsappTemplate + DefaultSettings
resources/views/
├── components/app-logo.blade.php
├── livewire/...
├── pdf/receipt.blade.php
└── (Breeze defaults)
routes/
├── web.php       # auth + admin + teacher + livewire
├── webhooks.php  # BayarCash + Sendora callbacks (no CSRF)
└── console.php   # schedule
tests/Feature/    # 5 feature test files (40 tests)
```

## Troubleshooting

- **Blank logo / 404**: run `php artisan storage:link`.
- **BayarCash callback returns `invalid_checksum`**: secret key mismatch; check super-admin → Settings → BayarCash.
- **Sendora returns 403 (`API access only on Business plan`)**: upgrade Sendora subscription or use a different number.
- **Spatie cache holds stale roles after seeding**: `php artisan permission:cache-reset` (the seeder already calls this, but it's worth knowing).
- **Queued notifications never run**: ensure `php artisan queue:work` is running and `QUEUE_CONNECTION=database` (or redis) is set.

## License

MIT.
