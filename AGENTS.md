# Repository Guidelines

## Project Structure & Module Organization

This repository contains a Laravel API backend, a Nuxt frontend, and Maizzle email templates. Backend code is in `backend/app`, routes in `backend/routes`, config in `backend/config`, and Pest tests in `backend/tests`. Nuxt pages, layouts, components, composables, middleware, and assets live under `frontend/`. Email templates and build scripts live in `emails/`; deploy output is copied into Laravel for previews and tests. Project notes are in `docs/`. See `backend/AGENTS.md` for deeper Laravel guidance.

## Build, Test, and Development Commands

- `npm run up`: start the Laravel Sail Docker stack from `backend/`.
- `npm run down`: stop the Sail stack.
- `npm run dev`: start Sail, queue listener, log tailing, Nuxt, Maizzle, and email sync.
- `cd frontend && npm run build`: build the Nuxt app for production.
- `cd emails && npm run deploy`: build email templates, sync images, and copy views to Laravel.
- `cd backend && composer test`: clear Laravel config and run the backend test suite.
- `./check.sh`: run Maizzle deploy, Pint, PHPStan/Larastan, Pest, Composer audit, and Nuxt build.

## Coding Style & Naming Conventions

Use Laravel conventions in `backend/`: PHP 8.3+, PSR-4 namespaces, 4-space indentation, and Pint formatting from `backend/pint.json`. Keep controllers thin; move validation, storage, and business logic into requests, services, actions, or models. Use PascalCase for Vue components, `useXxx.ts` for composables, and route-based filenames in `frontend/pages`. Keep translations in `frontend/i18n/locales` and `backend/lang`.

## Testing Guidelines

Backend tests use Pest with PHPUnit. Put endpoint and workflow tests in `backend/tests/Feature/...`; put isolated logic tests in `backend/tests/Unit/...`. Name tests after the behavior or endpoint area, for example `StoreTest.php` or `UploadTest.php`, and use descriptive `it(...)` cases. Email preview tests depend on generated Maizzle views, so run `cd emails && npm run deploy` first or use `./check.sh`.

## Commit & Pull Request Guidelines

Recent history uses short summary commits such as `composer update`, `fixes after testing`, and `Add admin email composer with passkey auth`. Keep commits focused, imperative when possible, and specific about behavior. Pull requests should include a concise description, linked issue or task, `./check.sh` results, and screenshots or sample payloads for UI, API, email, or media changes.

## Security & Configuration Tips

Do not commit `.env`, credentials, uploaded media, or generated caches. Keep service credentials in environment variables and document new required variables in setup notes. Review Sanctum, WebAuthn, enrollment, and admin authorization tests when changing authentication or access control.
