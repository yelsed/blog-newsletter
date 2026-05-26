# Repository Guidelines

## Project Structure & Module Organization

This is the Laravel backend for a blog project. Application code lives in `app/`, with API controllers under `app/Http/Controllers/Api`, form requests under `app/Http/Requests`, models under `app/Models`, and media storage abstractions under `app/Contracts` and `app/Services`. Routes are split between `routes/api.php`, `routes/web.php`, and `routes/console.php`. Database files live in `database/`. Blade views, email templates, CSS, JavaScript, and images live in `resources/`. Tests are organized as `tests/Feature/...` and `tests/Unit/...`.

## Build, Test, and Development Commands

- `composer setup`: install dependencies, create `.env`, generate the app key, migrate, and build assets.
- `composer dev`: run the Laravel server, queue listener, log tailing, and Vite concurrently.
- `composer test`: clear config and run the Laravel test suite.
- `composer check`: run Pint in check mode, PHPStan/Larastan level 8, Pest, and `composer audit`.
- `npm run dev`: start Vite only.
- `npm run build`: build frontend assets for production.
- `./vendor/bin/sail up`: start the Docker stack in `compose.yaml`.

## Coding Style & Naming Conventions

Use PHP 8.3+ and Laravel conventions. PHP files should declare strict types; `pint.json` enforces the Laravel preset plus `declare_strict_types`. Use 4-space indentation for PHP. Keep controllers thin by using form requests, services, actions, policies, and model methods. Name controllers by resource/action, for example `EmailController` or `SendTestController`; name request classes by intent, for example `StoreEmailRequest`.

## Testing Guidelines

The project uses Pest with PHPUnit underneath. Feature tests use `RefreshDatabase` through `tests/Pest.php`; shared helpers such as `adminUser()` are defined there. Place new endpoint tests near the feature area they cover, such as `tests/Feature/Admin/Email/StoreTest.php`. Prefer descriptive Pest `it(...)` cases and factories. Run `composer test` during development and `composer check` before opening a PR.

## Commit & Pull Request Guidelines

Recent commits use short, imperative or summary-style messages such as `composer update`, `fixes after testing`, and `Add admin email composer with passkey auth`. Keep commits focused and describe the behavioral change. Pull requests should include a concise description, related issue or task, test results from `composer check`, and screenshots or sample payloads when API responses, emails, or media behavior change.

## Security & Configuration Tips

Do not commit `.env` or secrets. Keep service credentials in environment variables and add safe defaults to config files. Authentication uses Sanctum and WebAuthn; review authorization tests when changing admin, enrollment, or passkey flows.
