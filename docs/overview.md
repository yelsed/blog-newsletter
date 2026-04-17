# Newsletter App — Current State

A snapshot of what exists in the repo today. This is the MVP foundation; blog/CMS features come later.

## What the app does

A visitor lands on the homepage, enters their email (and optionally a name), and receives a verification email. Clicking the link in the email confirms their subscription. They can unsubscribe at any time via a token link.

That's it. No campaigns, no admin UI, no content pages yet.

## Architecture

Decoupled: Laravel API ↔ Nuxt 3 SPA. Emails are built separately with Maizzle and compiled into Laravel Blade views.

```
┌─────────────┐      HTTP/JSON      ┌──────────────┐
│  Nuxt 3     │ ──────────────────▶ │  Laravel 13  │
│  (frontend) │                     │  (API)       │
└─────────────┘                     └──────┬───────┘
                                           │
                      ┌────────────────────┼────────────────────┐
                      ▼                    ▼                    ▼
                ┌──────────┐         ┌──────────┐         ┌──────────┐
                │PostgreSQL│         │  Redis   │         │ Mailpit  │
                │  (data)  │         │ (queue)  │         │  (dev)   │
                └──────────┘         └──────────┘         └──────────┘

┌─────────────┐     npm run deploy     ┌──────────────────────────┐
│  Maizzle    │ ─────────────────────▶ │ backend/resources/views/ │
│  (emails/)  │    .html → .blade.php  │ emails/*.blade.php       │
└─────────────┘                        └──────────────────────────┘
```

## Repo layout

```
blog-laravel-nuxt/
├── backend/          # Laravel 13 API
├── frontend/         # Nuxt 3 SPA
├── emails/           # Maizzle email templates
├── docs/             # This folder
├── check.sh          # Runs full quality suite
├── CLAUDE.md         # Claude Code project instructions
└── .claude/          # Hooks + settings
```

## Backend (Laravel 13)

**Stack**: PHP 8.3+, Laravel 13, PostgreSQL, Redis, Sanctum, Sail (Docker for local dev).

**Key packages** (spatie ecosystem):
- `spatie/laravel-data` — DTOs
- `spatie/laravel-permission` — authorization (not used yet, scaffolded)
- `spatie/laravel-queueable-action` — queueable actions
- `spatie/laravel-typescript-transformer` — generates TS types from DTOs

**Domain**: `Newsletter` (the only domain right now). Everything is namespaced under it.

### The Subscriber model

Single table. `backend/app/Models/Subscriber.php`:

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `email` | string | unique, indexed |
| `name` | string? | nullable |
| `email_verified_at` | datetime? | null until verified |
| `verification_token` | string(64) | generated in `booted()` |
| `subscribed_at` | datetime | set in `booted()` |
| `unsubscribed_at` | datetime? | null until opt-out |
| `created_at`, `updated_at` | | |

Scopes: `verified()` and `active()` (verified AND not unsubscribed).

### API endpoints

Prefixed with `/api`:

| Method | Endpoint | Controller | Throttle |
|--------|----------|-----------|----------|
| GET | `/api/health` | inline closure | — |
| POST | `/api/newsletter/subscribe` | `SubscribeController` | 5/min per IP |
| GET | `/api/newsletter/verify/{token}` | `VerifyController` | — |
| GET | `/api/newsletter/unsubscribe/{token}` | `UnsubscribeController` | — |

All controllers are invokable (single `__invoke` method).

### Request flow

```
POST /api/newsletter/subscribe {email, name?}
    │
    ▼
SubscribeRequest                 ← validates + prepareForValidation (lowercase/trim email)
    │
    ▼
SubscribeData (DTO)
    │
    ▼
SubscribeAction                  ← creates Subscriber, dispatches notification
    │
    ▼
Subscriber::create()             ← booted() generates verification_token
    │
    ▼
VerifySubscriptionNotification   ← queued (Redis), renders emails.verify-subscription view
    │
    ▼
Email to user with link: {FRONTEND_URL}/newsletter/verify?token={token}
```

### Security hardening (in `AppServiceProvider`)

- `Model::preventLazyLoading()` — catches N+1 queries in dev
- `Model::preventSilentlyDiscardingAttributes()` — catches `$fillable` typos
- Explicit `$fillable` on `Subscriber` (no `$guarded = []`)
- `immutable_datetime` casts on all date fields
- Rate limiter `newsletter` defined for public endpoints
- Strict types on every PHP file
- LaraStan level 8 (max strictness)

### Tests

17 Pest tests, SQLite in-memory:
- Subscribe: happy path, email normalization, duplicate rejection, validation errors, token generation
- Verify: valid token, invalid token, already verified
- Unsubscribe: valid, unverified, already unsubscribed, invalid token
- Model scopes: `verified`, `active`

## Frontend (Nuxt 3)

**Stack**: Nuxt 3, Vue 3, TailwindCSS v4 (Vite plugin), TypeScript.

### Pages

| Route | Purpose |
|-------|---------|
| `/` | Hero + newsletter signup form |
| `/newsletter/verify?token=...` | Verifies email via API call on mount |
| `/newsletter/unsubscribe?token=...` | Unsubscribes via API call on mount |

### Components

- `NewsletterForm.vue` — email + name inputs, handles loading/success/error states, surfaces Laravel validation errors
- `AppHeader.vue` — minimal nav
- `AppFooter.vue` — copyright

### Composable

`composables/useApi.ts` — wraps `$fetch` with the API base URL from `runtimeConfig.public.apiBase` (env var `NUXT_PUBLIC_API_BASE`, default `http://localhost/api`).

### Layout

`layouts/default.vue` — header + `<slot />` + footer in a flex column.

## Emails (Maizzle)

Tailwind-based HTML email framework. Dev preview lives at `http://localhost:3030`.

### Dual-mode variables

Templates use `page.*` variables that resolve differently based on environment:

| Variable | Dev (`config.js`) | Production (`config.production.js`) |
|----------|-------------------|-------------------------------------|
| `page.appName` | `'Newsletter'` | `'{{ $appName }}'` |
| `page.subscriberName` | `'Preview User'` | `'{{ $subscriberName }}'` |
| `page.verificationUrl` | real URL | `'{{ $verificationUrl }}'` |

In dev, you see real values. In production, Maizzle outputs Blade syntax that Laravel replaces at send time.

### Deploy pipeline

```
emails/emails/verify-subscription.html
        │
        │   npm run deploy  (in emails/)
        ▼
emails/build_production/verify-subscription.html   ← inlined CSS, purged, shortened
        │
        │   copy-to-laravel.js
        ▼
backend/resources/views/emails/verify-subscription.blade.php
```

Laravel's `VerifySubscriptionNotification` references it via `->view('emails.verify-subscription', [...])`.

### Current templates

- `verify-subscription.html` — the verification email with CTA button

## Quality tooling

- **Pint** (Laravel preset + `declare_strict_types`) — code style
- **LaraStan level 8** — static analysis
- **Pest** — tests
- **Composer audit** — dependency vulnerabilities
- **`./check.sh`** — runs everything including Nuxt build and Maizzle build

### Automated enforcement

- **Pre-push git hook**: runs Pint + LaraStan + Pest + audit before every push
- **Claude Code hook** (`.claude/hooks/php-quality-check.sh`): on every PHP file edit, auto-fixes Pint and runs LaraStan on the file; blocks with errors if analysis fails

## Local dev quick reference

```bash
# Backend
cd backend
./vendor/bin/sail up -d            # pgsql + redis + mailpit
./vendor/bin/sail artisan migrate

# Frontend
cd frontend && npm run dev         # http://localhost:3000

# Emails (preview)
cd emails && npm run dev           # http://localhost:3030

# Mailpit UI
# http://localhost:8025

# Quality check everything
./check.sh
```

## What's explicitly NOT built yet

- Blog / CMS / content pages
- Admin UI
- Campaign sending (only transactional email: the verification)
- Authentication for users beyond Sanctum scaffold
- Production deployment (Forge, K8s) — documented in [`docs/post-setup-phases.md`](./post-setup-phases.md)
- CI/CD (GitHub Actions)
