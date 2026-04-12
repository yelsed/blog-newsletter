# Newsletter — Laravel API + Nuxt 3 Monorepo

## Project Overview

A newsletter website where visitors can subscribe and browse content. Decoupled architecture: Laravel API backend + Nuxt 3 frontend + Maizzle email templates.

**Stack**: Laravel 13, Nuxt 3, TailwindCSS v4, PostgreSQL, Redis, Maizzle
**Local dev**: Laravel Sail (Docker Compose) for backend, `npm run dev` for frontend/emails
**Production**: Laravel Forge
**Testing**: Pest (PHP), SQLite in-memory for test DB

## Monorepo Structure

```
blog-laravel-nuxt/
├── backend/          # Laravel 13 API (Sail for local dev)
├── frontend/         # Nuxt 3 SPA
├── emails/           # Maizzle — Tailwind-based HTML email templates
├── check.sh          # Runs all quality checks across the monorepo
└── CLAUDE.md
```

## Quality Checks

Run all checks from the repo root:

```bash
./check.sh                        # Full suite: Pint, LaraStan, Pest, audit, Nuxt build, Maizzle build
cd backend && composer check       # Backend only: Pint, LaraStan, Pest, audit
```

**Pre-push hook**: Automatically runs backend checks (Pint, LaraStan, Pest, audit) before every `git push`.

**Claude Code hook**: Automatically runs Pint (auto-fix) and LaraStan on every PHP file edit. If LaraStan fails, fix the errors before continuing.

### Individual commands (from `backend/`):

```bash
./vendor/bin/pint --test           # Check code style (dry run)
./vendor/bin/pint                  # Fix code style
./vendor/bin/phpstan analyse --memory-limit=512M   # Static analysis (level 8)
./vendor/bin/pest                  # Run tests
composer audit                     # Security vulnerability check
```

## Backend Conventions

### Architecture

- **Domain namespacing**: All feature code lives under its domain namespace (e.g., `Newsletter/`)
  - `app/Actions/Newsletter/` — Business logic (queueable only where needed)
  - `app/Data/Newsletter/` — DTOs (spatie/laravel-data)
  - `app/Http/Controllers/Api/Newsletter/` — Single-action invokable controllers (`__invoke`)
  - `app/Http/Requests/Newsletter/` — Form Requests with validation
- **File generation**: ALWAYS use `php artisan make:*` to create Laravel files. Never create them manually.
- **Strict types**: Every PHP file starts with `declare(strict_types=1);`
- **LaraStan level 8**: No `@phpstan-ignore` unless truly justified
- **Pint**: Laravel preset with `declare_strict_types` rule

### Packages (spatie ecosystem)

- `spatie/laravel-data` — DTOs and API resources
- `spatie/laravel-permission` — Authorization
- `spatie/laravel-queueable-action` — Only on actions that should be queued
- `spatie/laravel-typescript-transformer` — Generate TS types from Data objects

### Security

- `Model::preventLazyLoading()` in dev — catches N+1 queries
- `Model::preventSilentlyDiscardingAttributes()` in dev — catches `$fillable` typos
- Explicit `$fillable` on all models (no `$guarded = []`)
- Rate limiting on public endpoints (defined in `AppServiceProvider`)
- `prepareForValidation()` on Form Requests to normalize input
- `immutable_datetime` casts for all date fields

### Testing

- **Pest** (not PHPUnit) — tests in `backend/tests/Feature/` and `backend/tests/Unit/`
- Tests use SQLite in-memory (`phpunit.xml` overrides `DB_CONNECTION=sqlite`)
- Run with `./vendor/bin/pest` from `backend/`

### API Endpoints

All endpoints are prefixed with `/api` (Laravel's `routes/api.php`).

| Method | Endpoint                         | Controller                    |
|--------|----------------------------------|-------------------------------|
| GET    | `/api/health`                    | Inline closure                |
| POST   | `/api/newsletter/subscribe`      | SubscribeController (throttled) |
| GET    | `/api/newsletter/verify/{token}` | VerifyController              |
| GET    | `/api/newsletter/unsubscribe/{token}` | UnsubscribeController    |

## Frontend Conventions

### Stack

- **Nuxt 3** with file-based routing (`pages/` directory)
- **TailwindCSS v4** via Vite plugin (not PostCSS)
- **Composables** in `composables/` — e.g., `useApi()` wraps `$fetch` with API base URL
- **Layouts** in `layouts/` — `default.vue` provides header/footer chrome

### Runtime Config

API base URL is set via `NUXT_PUBLIC_API_BASE` env var (defaults to `http://localhost/api`).

### Development

```bash
cd frontend && npm run dev        # Dev server on http://localhost:3000
cd frontend && npm run build      # Production build check
```

## Email Templates (Maizzle)

Emails are built with [Maizzle](https://maizzle.com/) — a Tailwind CSS framework for HTML emails. This produces battle-tested, inlined HTML that works across email clients.

### How it works

1. **Design** templates in `emails/emails/*.html` using Tailwind classes and Maizzle components
2. **Preview** with `npm run dev` in `emails/` — live reload on `http://localhost:3030`
3. **Build** with `npm run build` — outputs inlined HTML to `emails/build_production/`
4. **Deploy** with `npm run deploy` — builds AND copies to `backend/resources/views/emails/` as `.blade.php` files

### Variable system (dual-mode)

Templates use `page.*` variables that resolve differently per environment:

| Variable | Dev preview (`config.js`) | Production build (`config.production.js`) |
|----------|---------------------------|-------------------------------------------|
| `page.appName` | `'Newsletter'` | `'{{ $appName }}'` (Blade syntax) |
| `page.subscriberName` | `'Preview User'` | `'{{ $subscriberName }}'` |
| `page.verificationUrl` | `'http://localhost:3000/...'` | `'{{ $verificationUrl }}'` |

**Dev**: Variables render as real values for visual preview.
**Production**: Variables render as Blade `{{ $var }}` syntax. After the `.html` → `.blade.php` rename, Laravel processes them at send time.

### Adding a new email template

1. Create `emails/emails/your-template.html` using Maizzle layout/components
2. Add preview variables to `emails/config.js` (dev) and Blade variables to `emails/config.production.js` (production)
3. Run `npm run deploy` from `emails/` to build and copy to Laravel
4. Reference in Laravel notification: `->view('emails.your-template', [...])`

### Key files

- `emails/config.js` — Base/dev config with preview variables, dev server on port 3030
- `emails/config.production.js` — Production config: CSS inlining, purging, Blade variable syntax
- `emails/scripts/copy-to-laravel.js` — Copies built HTML to `backend/resources/views/emails/`, renames `.html` → `.blade.php`

## Local Development Setup

```bash
# Backend (requires Docker for Sail)
cd backend
cp .env.example .env
./vendor/bin/sail up -d            # Starts pgsql, redis, mailpit
./vendor/bin/sail artisan migrate

# Frontend
cd frontend
npm install
npm run dev                        # http://localhost:3000

# Emails
cd emails
npm install
npm run dev                        # http://localhost:3030 (preview)
npm run deploy                     # Build + copy to Laravel views

# Local email testing
# Mailpit UI: http://localhost:8025 (comes with Sail)
```
