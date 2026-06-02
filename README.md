# Invoice

A self-hosted, multi-tenant invoicing application built with Laravel 12 (PHP 8.5), served via Laravel Octane on FrankenPHP.

Free for self-hosting. Offering this software as a hosted/managed service to third parties requires a commercial license — see [License](#license) below.

## Features

- **Multi-tenant** — each company has fully isolated data, users, customers, invoices, and settings
- **Invoices** — drafts, sending, payment tracking, cancellation invoices, PDF generation (DomPDF)
- **Recurring invoices** — auto-generate invoices on a configurable schedule
- **Customer management** — per-company customer lists with payment terms and VAT settings
- **DATEV export** — generate exports for the German tax authority (DATEV format)
- **Email delivery** — queued sending with status tracking (Queued → Sending → Sent / Error)
- **Role-based access** — built on Laravel Jetstream with team support
- **Demo data seeder** — for local development and exploration

## Requirements

- Docker
- Docker Compose
- Make

## Quickstart

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/invoice.git
cd invoice

# 2. Copy the environment template
cp src/.env.example src/.env

# 3. Start the stack
make run

# 4. In a new terminal, run migrations and seed demo data
make fresh
```

Then open:
- App: http://localhost:8090
- Adminer (DB admin): http://localhost:8091
- Mailpit (captures outgoing mail): http://localhost:8125

Demo login credentials are printed by the seeder.

## Architecture

The Laravel application lives in `src/`. The repository root holds the Docker compose file, the Makefile, and developer tooling.

The dev stack runs in Docker:

- **`invoice-web`** — built from `docker/web/Dockerfile` on top of `serversideup/php:8.5-frankenphp`. A single entrypoint script (`docker/web/dev-entrypoint.sh`) runs Laravel Octane (FrankenPHP driver, with `--watch` for hot reloads), the queue worker, and the scheduler in the same container.
- **`invoice-mysql`** — MySQL 8.
- **`invoice-mailpit`** — captures outgoing mail (UI on `:8125`, SMTP on `:11025`).
- **`invoice-adminer`** — DB UI on `:8091`.

Frontend assets are served by **Vite** running on the host (`cd src && npm run dev`) — Node is not installed in the web container.

Two documents describe the codebase in depth:

- [`CONTEXT.md`](CONTEXT.md) — canonical domain vocabulary (Company, Recurring Invoice, Mail Status, etc.)
- [`CLAUDE.md`](CLAUDE.md) — codebase architecture guide, common commands, and code-quality gates

## Development

See [CONTRIBUTING.md](CONTRIBUTING.md) for local setup, coding standards, tests, and how to submit changes.

## License

This project is licensed under the [Elastic License 2.0](LICENSE).

In plain language:

- ✅ You may use, copy, modify, and self-host the software for free, including for commercial purposes inside your own organization.
- ❌ You may not provide the software to third parties as a hosted or managed service (SaaS) without a separate commercial license.

For SaaS licensing, contact tobias.redmann@gmail.com.
