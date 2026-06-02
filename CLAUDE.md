# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Working environment

The Laravel application lives in `src/` — treat `src/` as the Laravel project root. The repository root holds `docker-compose.yml`, the `Makefile`, and developer tooling.

Local development runs in Docker. The web container `invoice-web` is built from `docker/web/Dockerfile` on top of `serversideup/php:8.5-frankenphp`, with Xdebug, Node 22, and `chokidar` baked in. It runs three processes via `docker/web/dev-entrypoint.sh`:

1. **Laravel Octane** (FrankenPHP driver) serving HTTP on port `8080` (exposed as `localhost:8090`), with `--watch` so PHP changes reload workers automatically.
2. **`queue:work`** — Laravel queue worker.
3. **`schedule:work`** — Laravel scheduler.

The DB container is `invoice-mysql`. Mailpit (`invoice-mailpit`) and Adminer (`invoice-adminer`) round out the stack.

## Common commands

All commands run from the repository root unless noted. They source `./src/.env` and exec inside the `invoice-web` container.

- `make run` (alias `make start`) — bring the stack up (web on `:8090`, adminer `:8091`, mailpit UI on `:8125` / SMTP on `:11025`)
- `make stop` — stop containers
- `make rebuild` — rebuild the web image without cache (use after changes to `docker/web/`)
- `make ssh` — shell into the web container
- `make migrate` / `make rollback` / `make fresh` — DB migrations (`fresh` re-seeds)
- `make tinker` — artisan tinker
- `make test` — clears config caches, runs `migrate:fresh` on the testing DB, then `phpunit`
- `make quality` — `phpstan analyse` (level 5) + `pint --test`
- `make fix` — run Pint to auto-format `app/`
- `make clearall` — clear cache/route/config/view caches

Octane keeps the framework in memory between requests, so after editing things that bypass the `--watch` list (e.g. published vendor files, anything outside `app/`, `bootstrap/`, `config/`, `database/`, `routes/`, `public/**/*.php`, `resources/**/*.php`, `composer.lock`, `.env`) run `make ssh` and then `php artisan octane:reload` — or just `docker-compose restart invoice-web`.

Run a single test (from inside the container via `make ssh`):
```
./vendor/bin/phpunit --filter TestClassOrMethodName
./vendor/bin/phpunit tests/Feature/Some/Path/SomethingTest.php
```

Frontend assets (Vite, run on the **host** from `src/`, not inside the container): `npm run dev` (dev server with HMR on `:5173`), `npm run build` (production build to `public/build/`). Node is not installed in the web container by design — only `chokidar` is, for Octane's `--watch`.

## Ubiquitous language (read `CONTEXT.md`)

`CONTEXT.md` at the repo root defines the canonical domain vocabulary. Several domain terms differ from the class/column names still in the code — this divergence is intentional and being renamed in stages (see `docs/renames/`). Notably:

- **Company** in the domain ↔ `Tenant` / `TenantService` / `tenant_id` in code
- **Recurring Invoice** ↔ `MasterInvoice` / `MasterLineItem` / `MasterInvoiceController`
- **Company Profile** ↔ `GeneralInfo`
- **Company Legal Info** ↔ `LegalInfo`
- **Mail Status** values: Queued = `mailable`, Sending = `mailing`, Sent = `mailed`, Send Error = `mail error`, Not Queued = `not mailable`
- **Service Period** ↔ `performed_when`; **Due Date** ↔ `date_due`; **Payment Terms** ↔ `days_till_due`; **Customer Number** ↔ `customer_no`; **Invoice Number** ↔ `invoice_no`; **VAT Rate** ↔ `tax_rate`

When introducing new code, prefer the ubiquitous-language term. When touching existing code, follow the surrounding naming — large renames are tracked in `docs/renames/` and should not be done ad-hoc.

## Architecture

Laravel 12 (PHP 8.5) multi-tenant invoicing app, served in dev via Laravel Octane on FrankenPHP (`OCTANE_SERVER=frankenphp`). Each **Company** (`Tenant`) owns its **Users**, **Customers**, **Invoices**, **Recurring Invoices** (`MasterInvoice`), and **Settings**. Tenant scoping is mediated by `App\Services\TenantService` and the `TracksTenant` trait — keep tenant boundaries explicit when adding queries.

**Octane caveat:** because workers are long-lived, code must not rely on per-request global state. Avoid static caches, request-bound singletons that leak between requests, and `env()` calls outside config (which Octane's [docs flag](https://laravel.com/docs/12.x/octane#dependency-injection-and-octane) as common footguns). Stick to constructor injection and the container's request-scoped bindings.

HTTP middleware, exception handling, and console scheduling are wired in `bootstrap/app.php`. Scheduled tasks live in `routes/console.php`.

Code layout under `src/app/`:

- `Models/` — Eloquent models. Tenant-aggregate models (`Tenant`, `GeneralInfo`, `LegalInfo`) live under `Models/Tenant/`. The `Money` model is a value object that wraps integer cents.
- `Http/Controllers/` — request handling, organised by resource (`InvoiceController`, `MasterInvoiceController`, `CustomerController`, …) with sub-namespaces for `Admin/`, `API/`, `Datev/`, `Tenant/`.
- `Http/Requests/`, `Resources/`, `Responses/` — Form Requests, API resources, response objects.
- `Services/` — domain services grouped per aggregate (`Invoices/`, `MasterInvoices/`, `Customers/`, `LineItems/`, `InvoiceDocuments/`, `SettingService/`, `Shared/`, plus `TenantService.php`). Business logic belongs here, not in controllers.
- `Actions/` — single-purpose action classes (`Actions/Invoice/`, plus Jetstream/Fortify hooks).
- `Jobs/`, `Mail/` — queued work (invoice generation, PDF rendering, emailing).
- `Modules/InvoiceTemplates/` — PDF/Blade templates used to render Invoice Documents (`BladeInvoiceTemplate`).
- `Policies/`, `Rules/`, `Traits/`, `Helpers/`, `Enums/`, `View/Components/`, `Imports/` — supporting concerns.

Routes are split by audience: `routes/web.php`, `routes/admin.php`, `routes/jetstream.php`, `routes/api_V1.php`, `routes/api_V2.php`, and per-aggregate tenant-scoped files in `routes/tenant/` (`tenants.php`, `settings.php`, `generalInfos.php`, `legalInfos.php`).

Frontend stack: Livewire 4 + Blade + Jetstream 5 + Bulma, built with Vite. Views and JS/SCSS sources are in `src/resources/`.

Auth: Laravel Jetstream (with Sanctum for API tokens). Background work runs via standard Laravel queues; PDF generation uses `barryvdh/laravel-dompdf`; Excel imports/exports via `maatwebsite/excel`; backups via `spatie/laravel-backup`.

## Testing

PHPUnit suites split into `tests/Feature/` and `tests/Unit/` (configured in `src/phpunit.xml`). `make test` always re-migrates the testing DB before running, so feature tests can assume a clean schema. Testing env uses `array` mail/cache/session drivers and a `sync` queue.

## Code quality gates

- **PHPStan** (`larastan` extension) at **level 5**, configured in `src/phpstan.neon`. Excludes `*Filter.php`, `*Resource.php`, and Fortify's `CreateNewUser.php`.
- **Pint** for formatting (`pint.json` at repo root in `src/`).

`make quality` runs both in `--test` mode and is the canonical pre-commit check. Run `make fix` to auto-format.

## Renaming work in flight

`docs/renames/` contains a staged plan for renaming `Tenant → Company` and `MasterInvoice → Recurring Invoice` and supporting models/columns. Each doc lists "Do NOT touch in this pass" boundaries. When working on renames, follow those docs rather than improvising — they exist to keep blast radius bounded (e.g. DB table/column names and the `App\Models\Tenant\` namespace are intentionally kept in early passes).
