# Contributing to Invoice

Thank you for considering a contribution. This document covers how to get the project running locally, what's expected from a pull request, and the conventions the codebase uses.

## Code of Conduct

This project adheres to a [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree to uphold it.

## Local Setup

**Prerequisites:** Docker, Docker Compose, Make. Optional but recommended for working on frontend assets: Node 22+ (to run Vite on the host).

1. Fork and clone the repository
2. Copy `src/.env.example` to `src/.env`
3. Run `make run` — this builds the FrankenPHP-based web image and brings the stack up. The web container runs Laravel Octane, the queue worker, and the scheduler together; PHP changes hot-reload via Octane's `--watch`.
4. In a new terminal, run `make fresh` to migrate and seed demo data
5. (Optional) For frontend work, run `cd src && npm install && npm run dev` on the host to start Vite (HMR on `:5173`). Node is intentionally not installed in the web container.
6. Open http://localhost:8090

Useful commands (all run from the repo root):

| Command | Purpose |
| --- | --- |
| `make run` / `make start` | Bring the Docker stack up |
| `make stop` | Stop containers |
| `make rebuild` | Rebuild the web image without cache (after `docker/web/` changes) |
| `make ssh` | Shell into the web container |
| `make migrate` | Run pending migrations |
| `make fresh` | Drop, re-migrate, and re-seed the DB |
| `make tinker` | Open `artisan tinker` |
| `make test` | Run the full PHPUnit suite (against a fresh test DB) |
| `make quality` | Run PHPStan + Pint in check mode |
| `make fix` | Auto-format with Pint |
| `make clearall` | Clear cache, route, config, and view caches |

### Octane gotchas

Because Octane keeps the framework booted in memory, a couple of things differ from a vanilla PHP-FPM setup:

- **Reloads.** Files listed in `config/octane.php` under `watch` (e.g. `app/`, `routes/`, `config/`) reload automatically. For anything else, run `php artisan octane:reload` from `make ssh`, or `docker-compose restart invoice-web`.
- **Request-scoped state.** Avoid static caches, request-bound singletons, and `env()` calls outside `config/*` — these are common Octane footguns. See the [Octane docs](https://laravel.com/docs/12.x/octane#dependency-injection-and-octane).

## Running a Single Test

From inside the web container (`make ssh`):

```bash
./vendor/bin/phpunit --filter TestClassOrMethodName
./vendor/bin/phpunit tests/Feature/Some/Path/SomethingTest.php
```

## Code Quality

Two gates must pass before a PR is reviewed:

- **PHPStan** at level 5 (`larastan` extension) — configured in `src/phpstan.neon`
- **Pint** formatting — configured in `src/pint.json`

Run `make quality` to check both. Run `make fix` to auto-format.

Both checks are wired up as Husky pre-commit and pre-push hooks, so you'll get fast feedback locally.

## Domain Language

This codebase has a canonical ubiquitous language documented in [`CONTEXT.md`](CONTEXT.md). When introducing new code, prefer the domain term over the legacy code name:

- **Company**, not `Tenant`
- **Recurring Invoice**, not `MasterInvoice`
- **Due Date**, not `date_due`
- **VAT Rate**, not `tax_rate`

When touching existing code, follow the surrounding naming — large renames are tracked under `docs/renames/` and should not be done ad-hoc inside unrelated PRs.

## Pull Request Process

1. Create a branch off `main` with a descriptive name
2. Keep the PR focused — one concern per PR
3. The PR description should explain *why* the change is needed, not just *what* changed (the diff already shows what)
4. Ensure `make quality` and `make test` both pass locally
5. Open the PR against `main`

## Reporting Issues

Please use [GitHub Issues](../../issues) with:

- A clear description of the problem
- Steps to reproduce
- Expected vs. actual behaviour
- Your environment (PHP version, Docker version, OS)

For security-related issues, please email tobias.redmann@gmail.com directly rather than opening a public issue.
