# Tests

PHPUnit suites live under `Feature/` and `Unit/`. Run via `make test` from the repo root, or `./vendor/bin/phpunit` inside the `invoice-web` container.

## Choosing a database trait

| Test type | Trait | Reason |
|---|---|---|
| HTTP / controller / Livewire | `RefreshDatabase` | Matches existing `Feature/Http/...` convention; safer with HTTP middleware. |
| Service / Job / Action unit | `DatabaseTransactions` | Faster, matches `SubscriptionHandlerTest`; required when asserting mid-transaction state. |
| Seeder | `DatabaseTransactions` | Matches existing `DemoSeederTest`. |

If your test needs to force-throw mid-`DB::transaction`, use `DatabaseTransactions` so you can inspect the rollback.

## Shared concerns (under `tests/Concerns/`)

- `MakesTenants::makeTenantWithEverything()` — fully-wired tenant: owner User, GeneralInfo, LegalInfo, Customer with MailReceiver, and 6 seeded Settings (email sender + SMTP config). Use instead of duplicating the ~15-line setup.
- `AssertsMoney::assertCentsEqual($expected, $actual)` and `assertInvoiceTotalsMatchLineItems(Invoice)`.
- `FakesInvoicePipeline::fakeInvoicePipeline()` — wraps `Mail::fake() + Queue::fake() + Storage::fake('local')`.
- `FakesInvoicePipeline::assertPdfStoredFor(Invoice)` — asserts the invoice has an `InvoiceDocument` of type `TYPE_INVOICE_DOCUMENT` whose `path` exists on the faked `local` disk.
- `FakesInvoicePipeline::assertInvoiceMailedTo(Invoice, string $email)` — asserts `InvoiceMail` was sent to `$email`.
- `CrossTenantMatrix::assertEndpointRejectsCrossTenant($method, $urlBuilder, $payload = null)` — used by `Feature/TenantIsolation/*`.

Opt in with a `use` statement; do not add traits to `Tests\TestCase`.

## Naming conventions

- Test methods: `testWhatHappensWhenContext` (matches existing suite).
- Prefer ubiquitous-language terms from `CONTEXT.md` in new test names and comments (Company, Recurring Invoice, Service Period) even when the underlying class is `Tenant` / `MasterInvoice`.
- Existing test names are not retroactively renamed.

## Faking discipline

- Any test that touches PDF output: `Storage::fake('local')`.
- Any test that touches mail: `Mail::fake()`.
- One PDF smoke test in `Feature/Jobs/GeneratePDFTest.php` exercises the real weasyprint renderer (skipped when the binary is not on PATH) — all other PDF tests mock or fake.
- No test should talk to real SMTP, real disk, or a real queue worker.

## Pinning vs fixing

Tests in the top-6 testing-gaps phases follow three categories:
1. **Happy path** — confirms current correct behavior.
2. **Pinned behavior** — documents current behavior even when surprising; comment with `// pinning current behavior; see follow-up`.
3. **Required failure** — asserts specific exception or HTTP status; "if this breaks, prod breaks".

When a small fix is applied, the pinned-behavior assertion is replaced with the corrected-behavior assertion in the same commit. Commit message format: `test(area): cover X (also fixes Y)`.
