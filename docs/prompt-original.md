# Claude Code Prompt — School Teacher Fee Collection System

> Copy everything below the divider into Claude Code as your initial prompt. Edit the bracketed `[ ]` placeholders before running.

---

## Project Brief

Build a **monthly fee collection web application** for a Malaysian school where ~40 teachers log in to view and pay their monthly fees. The app must support online payment via **BayarCash** (FPX, DuitNow) and send **WhatsApp notifications** via **Sendora** (https://sendora.cc) for invoice issuance, payment confirmation, and overdue reminders. An admin dashboard manages teachers, fee structures, invoices, payments, reports, and branding (including a school logo upload).

## Tech Stack (use exactly these — do not substitute)

- **Laravel 12** (latest stable)
- **Livewire 3** (primary interactivity layer — no Vue, no Inertia, no React)
- **Tailwind CSS** (latest, via Vite)
- **Alpine.js** (small client-side bits only — comes bundled with Livewire)
- **MySQL 8** or **MariaDB 10.6+**
- **Laravel Breeze (Livewire stack)** for auth scaffolding
- **spatie/laravel-permission** for roles (`teacher`, `admin`, `super-admin`)
- **barryvdh/laravel-dompdf** for PDF receipts
- **maatwebsite/excel** for report exports
- **webimpian/bayarcash-php-sdk** (official BayarCash SDK)
- **intervention/image** for logo handling/resizing

## Roles & User Stories

**Teacher** can:
- Log in with email/password
- View their profile and update phone number
- See list of their invoices (paid, unpaid, overdue) for the current and past years
- Click "Pay Now" on an unpaid invoice → redirected to BayarCash → returns to a payment status page
- Download PDF receipts for paid invoices
- Receive WhatsApp notifications (invoice issued, payment received, overdue reminder)

**Admin** can:
- Everything a teacher can do, plus:
- CRUD teachers (name, IC number, email, phone, monthly fee amount, active/inactive)
- Bulk import teachers from CSV
- Configure fee structure (default monthly amount, due day of month, late fee policy)
- Generate monthly invoices for all active teachers (manual button + scheduled job on day 1 of each month)
- View all invoices and payments with filters (month, status, teacher)
- Manually mark invoices as paid (for cash/cheque payments) with note + receipt upload
- Resend invoice or reminder via WhatsApp on demand
- View dashboard with collection KPIs (collected this month, outstanding, % paid, top overdue teachers)
- Export monthly collection reports to Excel and PDF
- **Upload and manage school logo** (used in PDF receipts, login page, dashboard header, email/WhatsApp messages)
- Configure WhatsApp message templates (with variables like `{{teacher_name}}`, `{{amount}}`, `{{month}}`, `{{due_date}}`)
- View notification log (every WhatsApp send: timestamp, recipient, status, error if failed)

**Super-admin** can:
- Everything an admin can do, plus:
- Manage admin users
- Edit BayarCash credentials, Sendora API key, and other system settings from UI (encrypted at rest)
- Toggle sandbox/live mode for BayarCash

## Database Schema (high level)

Use Laravel migrations. Suggested tables:

- `users` (Breeze default + add `phone`, `ic_number`, `monthly_fee_amount`, `is_active`)
- `roles`, `permissions`, `model_has_roles`, etc. (from Spatie)
- `fee_structures` — id, name, amount, due_day, late_fee_amount, late_fee_grace_days, is_default
- `invoices` — id, user_id, fee_structure_id, invoice_number (unique, format `INV-YYYYMM-####`), period_month (date, first of month), amount, late_fee, total, due_date, status (`pending`, `paid`, `overdue`, `cancelled`), paid_at, notes
- `payments` — id, invoice_id, user_id, amount, method (`bayarcash`, `manual_cash`, `manual_transfer`, `manual_cheque`), bayarcash_transaction_id, bayarcash_exchange_reference, bayarcash_payment_channel, status (`pending`, `successful`, `failed`, `refunded`), receipt_path, paid_at, recorded_by (admin user id, nullable for online), raw_callback_payload (json)
- `notifications_log` — id, user_id, channel (`whatsapp`, `email`), template_key, recipient, payload (json), provider_message_id, status (`queued`, `sent`, `delivered`, `failed`), error, sent_at
- `whatsapp_templates` — id, key (unique, e.g., `invoice_issued`), name, body_template, is_active
- `settings` — key/value store for school name, logo_path, BayarCash creds, Sendora creds, default fee amount, etc. (use a settings package like `spatie/laravel-settings` OR a simple key/value table — your choice)
- `audit_logs` — id, user_id, action, subject_type, subject_id, changes (json), ip_address, created_at

Add appropriate indexes (user_id+period_month unique on invoices, status, due_date, etc.) and foreign keys with sensible cascade rules.

## BayarCash Integration

Use the **official Webimpian PHP SDK**: `composer require webimpian/bayarcash-php-sdk`

Read the SDK docs first: https://github.com/webimpian/bayarcash-php-sdk and https://docs.bayarcash.com

Implement a `BayarcashService` class wrapping the SDK with these methods:
- `createPaymentIntent(Invoice $invoice): string` — returns checkout URL, stores payment row with `pending` status
- `verifyCallback(array $payload): bool` — checksum verification using API secret
- `handleCallback(array $payload): void` — server-to-server confirmation; updates payment + invoice; idempotent (use `bayarcash_transaction_id` as idempotency key)
- `handleReturn(array $payload): array` — for the user-facing return page

**Required environment variables:**
```
BAYARCASH_API_TOKEN=
BAYARCASH_API_SECRET_KEY=
BAYARCASH_PORTAL_KEY=
BAYARCASH_SANDBOX=true
BAYARCASH_API_VERSION=v3
BAYARCASH_RETURN_URL=
BAYARCASH_CALLBACK_URL=
```

**Routes:**
- `GET /pay/{invoice}` — auth-protected, generates payment intent, redirects to BayarCash
- `GET /payment/return` — user lands here after payment; show success/failure UI
- `POST /payment/callback` — public route, no CSRF, verifies checksum, updates DB, triggers WhatsApp notification on success

**Critical:** the callback handler must be idempotent and must complete fast. Dispatch the WhatsApp notification to a queue, do not send synchronously.

## Sendora WhatsApp Integration

**Step 1 (mandatory before coding):** Read the Sendora API documentation at https://sendora.cc/docs. Identify:
- Base URL
- Authentication method (likely Bearer token or API key in header)
- Endpoint for sending a text message
- Phone number format expected (with or without `+`, country code handling)
- Response shape for success and errors
- Webhook payload format if delivery callbacks are supported
- Rate limits

**Step 2:** Build `SendoraService` class as a thin wrapper. Keep all Sendora-specific logic inside this one class so the provider can be swapped later.

**Step 3:** Build a custom Laravel Notification channel (`SendoraChannel`) that uses `SendoraService`. Notifications must:
- Implement `ShouldQueue`
- Have exponential backoff retry (3 attempts: 30s, 2min, 10min)
- Log every attempt to `notifications_log` table
- Format Malaysian numbers correctly: `0123456789` → `60123456789` (strip leading zero, prepend country code 60); also handle `+60123456789` and `60123456789` inputs

**Notifications to build:**
1. `InvoiceIssuedNotification` — sent when monthly invoice generated
2. `PaymentReceivedNotification` — sent when BayarCash callback confirms payment OR admin marks as paid
3. `PaymentReminderNotification` — sent 3 days before due date (scheduled job)
4. `PaymentOverdueNotification` — sent the day after due date (scheduled job)
5. `ManualReminderNotification` — sent on-demand by admin

Each notification reads its message body from the `whatsapp_templates` table so admins can edit copy without code changes. Variables in templates use Blade-style `{{ var }}` syntax replaced server-side.

**Required environment variables:**
```
SENDORA_API_KEY=
SENDORA_BASE_URL=
SENDORA_DEVICE_ID=     # if Sendora uses device/instance IDs
SENDORA_TIMEOUT=10
```

**Webhook (optional, only if Sendora supports delivery status):**
- `POST /webhooks/sendora` — verify signature if provided, update `notifications_log.status`

## Admin Dashboard — Logo Upload (explicit requirement)

Build a `Settings > Branding` page accessible to admin and super-admin. It must:

- Allow upload of school logo (PNG, JPG, SVG, WEBP, max 2MB)
- Validate dimensions (min 200x200, max 2000x2000)
- On upload, use `intervention/image` to:
  - Generate a 256x256 square version for PDF receipts and emails
  - Generate a 512x512 version for the dashboard header
  - Preserve aspect ratio with white/transparent padding
- Store originals in `storage/app/public/branding/` (run `php artisan storage:link`)
- Save paths in `settings` table keys: `logo_original`, `logo_small`, `logo_large`
- Show live preview of uploaded logo before save
- Show currently active logo with "Replace" and "Remove" buttons
- Apply the logo to:
  - Login page (above the form)
  - Sidebar/header on every authenticated page
  - PDF receipts (top-left corner)
  - Email notification headers
- Use a global Blade view component `<x-app-logo size="small|large" />` that reads from settings cache so changes apply instantly across the app
- Cache the logo settings (`Cache::rememberForever`) and bust the cache on update

Other branding settings on the same page: school name, address, contact email, contact phone, registration number, footer text for receipts.

## UI/UX Direction

- Clean, professional, school-appropriate (not flashy)
- Tailwind with a single primary color configurable in `tailwind.config.js` (start with `indigo-600`, but make it easy to change)
- Mobile-responsive (teachers will check on phones)
- Use Heroicons for iconography
- Sidebar navigation on desktop, hamburger drawer on mobile
- Toast notifications for actions (use `livewire-toaster` or build with Alpine)
- Loading states on every action (Livewire's `wire:loading` directives)
- Empty states with helpful illustrations/messages
- Confirmations for destructive actions (use Alpine modal or sweet-alert style)
- Format all currency as `RM 1,234.56`
- Format dates as `DD MMM YYYY` (e.g., `15 Jan 2026`) — use Carbon
- All text supports both English and Bahasa Malaysia (use Laravel localization, default `en`, build `ms` translations for all user-facing strings)

## Project Phases

Build in this order. After each phase, pause and let me review before moving on.

### Phase 1 — Foundation
- Fresh Laravel 12 install with Breeze (Livewire stack)
- Install Tailwind, Alpine, Spatie permissions, BayarCash SDK, DomPDF, Excel, Intervention Image
- Database migrations for all tables above
- Seed roles (teacher, admin, super-admin), one super-admin user, default fee structure, default WhatsApp templates
- Base layout with sidebar, header, logo component (placeholder logo)
- Login page with logo

### Phase 2 — Teacher Module (admin side)
- Teacher CRUD (Livewire components: `TeachersIndex`, `TeacherForm`, `TeacherShow`)
- CSV bulk import with validation and preview before commit
- Activate/deactivate toggle

### Phase 3 — Fee & Invoice Module
- Fee structure CRUD
- Manual "Generate invoices for [month]" action
- Scheduled command `php artisan invoices:generate-monthly` (run on 1st of each month)
- Invoices index for admin (filters: month, status, teacher)
- Teacher's own invoices view

### Phase 4 — BayarCash Integration
- Service class + routes + Livewire pay button
- Sandbox testing flow documented in README
- Callback handler with idempotency
- Manual payment recording for admin (cash/cheque/transfer with optional receipt upload)

### Phase 5 — Sendora WhatsApp Integration
- Read Sendora docs first
- Service class + custom notification channel + queue config
- All 5 notification classes
- Notification log viewer for admin
- WhatsApp template editor (admin can edit copy)
- Manual "Send reminder" button on invoice row

### Phase 6 — Admin Dashboard & Reports
- KPI dashboard (collected this month, outstanding, % paid, recent payments, top overdue)
- Monthly collection report with Excel export
- PDF receipt generator (DomPDF, uses logo + school details)

### Phase 7 — Settings & Branding
- Logo upload feature (full spec above)
- School details settings
- BayarCash and Sendora credential management (super-admin only, encrypted)
- WhatsApp template management UI

### Phase 8 — Polish
- Localization (English + Bahasa Malaysia)
- Audit logging on all create/update/delete actions
- Email notifications (parallel to WhatsApp where appropriate)
- Backup command setup (spatie/laravel-backup)
- README with deployment instructions for a Malaysian VPS (DigitalOcean Singapore, Exabytes, etc.)
- Basic Pest tests for: invoice generation, BayarCash callback idempotency, Sendora send, logo upload

## Code Quality Rules

- Follow Laravel conventions strictly (PSR-12, Eloquent over raw queries, form requests for validation)
- Use Livewire 3's class-based components, not the old style
- Every Livewire component has corresponding Blade view in `resources/views/livewire/`
- Service classes go in `app/Services/`
- Notifications in `app/Notifications/`
- Use enum classes for invoice status, payment method, payment status, notification status (PHP 8.1+ backed enums)
- Form Requests for all non-trivial validation
- Never put secrets in code — only `.env`
- All user input validated and sanitized
- Every queueable job/notification declares `tries`, `backoff`, `timeout`
- CSRF on all non-callback routes; callback routes verify checksum/signature instead
- Eager load relationships to prevent N+1 (use `model:without-fetching` checks)
- Database queries that return collections use pagination

## Deliverables Checklist

Before you say you're done, verify:

- [ ] `composer install && npm install && npm run build` works on a fresh clone
- [ ] `php artisan migrate --seed` runs cleanly
- [ ] Default super-admin can log in and reach the admin dashboard
- [ ] A test teacher can log in, see invoices, click "Pay Now", complete a sandbox BayarCash payment, return to success page, and see the invoice marked paid
- [ ] WhatsApp notification fires (or queues) on payment success — verified in `notifications_log`
- [ ] Logo upload reflects within ~1 second across login page, header, and a generated PDF receipt
- [ ] Manual invoice generation creates one invoice per active teacher, no duplicates if run twice in same month
- [ ] Excel export downloads with correct data
- [ ] PDF receipt downloads with logo and school details
- [ ] All routes have proper auth + role middleware
- [ ] README covers: install, env setup, BayarCash sandbox setup, Sendora setup, deployment, scheduled task setup (`php artisan schedule:run` cron)

## What to Ask Me Before Starting

1. School name and any branding details I want pre-seeded (or leave generic)
2. Default monthly fee amount
3. Whether to use a settings package or roll our own key/value table
4. Whether teachers self-register or are only created by admin (default: admin-only)
5. Whether to support partial payments or only full-amount payments (default: full only)

Ask all of these in one batch, then proceed.

---

**Begin with Phase 1.** Show me the file tree after install and the migration list before running anything destructive.
