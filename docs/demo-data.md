# Demo data

This document describes the personas and data created by `DemoSeeder`. Use it as a map when smoke-checking the app: every invoice and customer has a story, so you can navigate by name rather than by row number.

For the design rationale and architecture, see [`superpowers/specs/2026-06-01-demo-seeder-design.md`](./superpowers/specs/2026-06-01-demo-seeder-design.md).

## How to seed

```bash
make fresh         # wipes DB, runs migrations, runs all seeders
```

Optional env vars (set them in `src/.env` before seeding):

| Variable | Purpose | If unset |
|---|---|---|
| `SEED_ADMIN_EMAIL` | Email for the platform-wide admin user | `admin@example.test` |
| `SEED_ADMIN_PASSWORD` | Password for the admin user | random, printed to console once |
| `SEED_DEMO_PASSWORD` | Shared password for all demo tenant users | random per user, printed to console once |

After seeding, the console prints which password was used so you can log in.

## The two companies at a glance

| | Kranich Software GmbH | Northwind Creative LLC |
|---|---|---|
| Location | Berlin, Germany | Brooklyn, NY, USA |
| Type | Software consultancy | Design studio |
| Owner | Anna Kranich | Maya Patel |
| Staff users | Anna + Lukas Berger | Maya only |
| Currency | EUR | USD |
| Default payment terms | 14 days | 14 days |
| Customers | 6 | 4 |
| One-off invoices | 16 | 8 |
| Recurring invoices | 6 | 2 |

The two tenants exist to exercise multi-tenancy. Kranich carries the bulk of edge-case coverage; Northwind is a smaller second world that adds USD and US/Canadian customers.

## Tenant 1 — Kranich Software GmbH

Small Berlin-based software consultancy, founded 2019. Mostly invoices German B2B customers in EUR, with two cross-border clients to exercise the VAT-exempt paths.

**Address:** Reichenberger Straße 124, 10999 Berlin
**Contact:** hallo@kranich-software.example · +49 30 12345-678
**Legal:** Amtsgericht Berlin-Charlottenburg, HRB 234567 B · VAT-ID DE123456789
**Bank:** Commerzbank Berlin · IBAN DE89 3704 0044 0532 0130 00

### People

**Anna Kranich** — founder and owner. Runs the consultancy, signs off invoices, owns customer relationships. Email: `anna@kranich-software.example`.

**Lukas Berger** — developer. Has user access for tracking his time and drafting invoices but doesn't manage the company. Email: `lukas@kranich-software.example`.

### Customers

Each Kranich customer is picked to exercise a specific VAT/billing scenario. When you're smoke-checking a tax-related change, walk through these in order:

**K-0001 — Müller Maschinenbau GmbH** (Stuttgart). Large German manufacturer; Kranich's biggest client. Standard 19 % VAT. Two billing email recipients (`rechnung@`, `buchhaltung@`) — they want every invoice routed to both. They have a monthly retainer and the most invoices of any customer, including the cancelled-invoice pair and the PDF-error invoice.

**K-0002 — Pixelblut Design GbR** (Berlin). Small agency that subcontracts CMS / hosting work to Kranich. Standard 19 % VAT. One billing email. They're a steady client but sometimes pay late and sometimes don't receive the email — that's where the *mildly-overdue* and *mail-error* invoices come from. Has the multi-VAT invoice (workshop + printed materials).

**K-0003 — Holzbau Eichenhain** (Bernau). Sole proprietor (Stefan Eichenhain). Registered as a Kleinunternehmer under §19 UStG, so Kranich invoices him at **0 % VAT** — no tax applied. Short 7-day payment terms because he asked for them. Two invoices and one weekly recurring template (still in draft).

**K-0004 — Verein für Stadtkultur e.V.** (Berlin). Non-profit cultural association. They get **7 % reduced VAT** on cultural-services line items. They were Kranich's first paid customer (back in late 2025); now they have a heavily-overdue invoice (~3 months) because their funding got frozen, and their monthly recurring template is **paused** for the same reason.

**K-0005 — Studio Bellini S.r.l.** (Roma, Italy). EU intra-community customer. **0 % VAT** with the reverse-charge note ("VAT to be paid by the recipient"). Has a daily recurring invoice — small, repeating usage-based billing.

**K-0006 — Acme Robotics Inc.** (San Jose, USA). Non-EU export customer. **0 % VAT** export. Has a half-yearly recurring subscription.

### What's in Kranich's books right now

These are the 16 one-off invoices, ordered the way you'd encounter them browsing:

1. **Müller Maschinenbau — Draft** — Anna is composing the next "Berlin Backend Phase 2" invoice, 3 line items, not yet sent.
2. **Pixelblut Design — Open, mail sent** — CMS migration project. 1 attachment (`project_report.pdf`). Due in 9 days.
3. **Holzbau Eichenhain — Paid** — Website relaunch. Settled two weeks ago. 0 % VAT (Kleinunternehmer), 7-day terms.
4. **Verein für Stadtkultur — Heavily overdue** — Kulturplattform maintenance Q1 2026. ~3 months past due. 7 % VAT. Stadtkultur is the cautionary tale.
5. **Pixelblut Design — Mildly overdue, mail failed** — February hosting bill. 21 days overdue, and the mailer reported a send error.
6. **Müller Maschinenbau — Paid, recurring-generated** — Monthly retainer April 2026. Generated automatically by Müller's monthly recurring template.
7. **Studio Bellini — Open, EU intra-community** — Consulting May 2026. 0 % VAT, reverse-charge note. Queued to be emailed.
8. **Acme Robotics — Open, non-EU export** — DevOps audit. 0 % VAT. Mail is in flight (status: Sending).
9. **Müller Maschinenbau — Cancelled** — Scope-dispute fallout. Voided. Paired with the next one.
10. **Müller Maschinenbau — Cancellation Invoice** — The legal reversal of #9. Negative totals. Carries its own invoice number from the shared sequence.
11. **Pixelblut Design — Multi-VAT** — Workshop (19 %) + printed workshop materials (7 %) on one invoice.
12. **Müller Maschinenbau — PDF Generation Error** — Issued, invoice number assigned, but the PDF render failed. Sits in the `open pdf error` state — visible in the UI but no document.
13. **Holzbau Eichenhain — Open with extended description** — Has a paragraph-long `detail_plus` extended description on its line item.
14. **Müller Maschinenbau — Draft with 10 line items** — Mix of consulting hours, travel costs, materials. Exercises long line-item lists.
15. **Pixelblut Design — Open with 2 attachments** — Project completion invoice with `project_report.pdf` and `time_log.pdf`.
16. **Verein für Stadtkultur — Paid (older)** — Q4 2025 setup invoice. Older paid history.

### Kranich's recurring invoices

| # | Customer | Status | Frequency | Next fires | Story |
|---|---|---|---|---|---|
| MK-1 | Müller Maschinenbau | Active | Monthly | in 5 days | Standard monthly retainer. Already generated invoice #6. |
| MK-2 | Pixelblut Design | Active | Quarterly | in 30 days | Quarterly support contract. |
| MK-3 | Acme Robotics | Active | Half-Yearly | in 60 days | Half-yearly cross-border subscription. |
| MK-4 | Verein für Stadtkultur | Paused | Monthly | — | Paused after the funding freeze. |
| MK-5 | Holzbau Eichenhain | Draft | Weekly | — | Drafted but not yet activated. |
| MK-6 | Studio Bellini | Active | Daily | tomorrow | Usage-based daily fee — the only Daily template. |

## Tenant 2 — Northwind Creative LLC

Single-founder design studio in Brooklyn, started 2022. Smaller scale than Kranich. Job in the demo: prove the app handles a second tenant, USD, and US/Canadian customers.

**Address:** 487 Bedford Avenue, Suite 3B, Brooklyn, NY 11211, USA
**Contact:** hello@northwindcreative.example · +1 (718) 555-0142
**Legal:** New York LLC · EIN 12-3456789
**Bank:** Chase Bank

### People

**Maya Patel** — founder, sole operator. Hires freelancers per project. Email: `maya@northwindcreative.example`.

### Customers

**N-0001 — Sunfire Coffee Co.** (Brooklyn). Local café chain. Maya does their visual identity work. Has a monthly social-media-graphics retainer (Northwind's only recurring template). Mix of draft + paid invoices.

**N-0002 — Linden & Quill, LLC** (Hudson, NY). Boutique book publisher. Maya designs covers — typically 3 cover designs per invoice. They had one project go sideways, which is why this customer carries Northwind's cancellation pair.

**N-0003 — Halcyon Yoga Studio** (Brooklyn). Single-location yoga studio. Tends to pay a bit late — has the mildly-overdue invoice. Also has a paused seasonal-campaign recurring template.

**N-0004 — Maple Leaf Marketing** (Toronto, Canada). Cross-border USD customer. Has an open illustration-project invoice that's waiting on payment.

### What's in Northwind's books

All in USD, all at 0 % tax (typical for US service businesses):

1. **Sunfire Coffee — Draft** — Brand refresh proposal. Composing.
2. **Sunfire Coffee — Paid** — Logo redesign. Paid three weeks ago.
3. **Linden & Quill — Open** — 3 book-cover designs as separate line items.
4. **Halcyon Yoga Studio — Open** — Web design project.
5. **Halcyon Yoga Studio — Overdue** — Brand guidelines doc, mildly past due.
6. **Maple Leaf Marketing — Open, cross-border** — Illustration project. Queued to be emailed.
7. **Linden & Quill — Cancelled** — Original of the cancellation pair.
8. **Linden & Quill — Cancellation Invoice** — Reverses #7.

### Northwind's recurring invoices

| # | Customer | Status | Frequency | Next fires |
|---|---|---|---|---|
| MN-1 | Sunfire Coffee Co. | Active | Monthly | in 10 days |
| MN-2 | Halcyon Yoga Studio | Paused | Quarterly | — |

## Coverage cheat-sheet

If you're checking a specific feature, here's where to look:

| What you're checking | Where to find an example |
|---|---|
| Draft invoice rendering | Kranich #1 or #14 (10 line items), Northwind #1 |
| Open invoice + due date math | Kranich #2, Northwind #3 |
| Paid invoice (recent) | Kranich #3, Northwind #2 |
| Paid invoice (old, archival) | Kranich #16 |
| Overdue invoice (mildly) | Kranich #5, Northwind #5 |
| Overdue invoice (badly) | Kranich #4 |
| Cancellation pair (Cancelled + Cancellation Invoice) | Kranich #9 + #10; Northwind #7 + #8 |
| PDF generation error state | Kranich #12 |
| Multi-VAT on a single invoice | Kranich #11 |
| 0 % VAT — Kleinunternehmer | Kranich #3, #13 (Holzbau Eichenhain) |
| 0 % VAT — EU intra-community / reverse charge | Kranich #7 (Studio Bellini) |
| 0 % VAT — non-EU export | Kranich #8 (Acme Robotics) |
| 7 % reduced VAT | Kranich #4, #16 (Verein für Stadtkultur), #11 (mixed) |
| 19 % standard VAT | Kranich #2, #5, #6, etc. |
| Currency: EUR | Kranich (any invoice) |
| Currency: USD | Northwind (any invoice) |
| Mail status: Not Queued | Drafts, cancellation invoices, PDF-error invoice |
| Mail status: Queued | Kranich #7 |
| Mail status: Sending | Kranich #8 |
| Mail status: Sent | Most opens |
| Mail status: Send Error | Kranich #5 |
| 7-day payment terms | Kranich #3, #13 |
| 14-day payment terms | Most invoices |
| 30-day payment terms | Kranich #6 |
| Invoice with one attachment | Kranich #2 |
| Invoice with multiple attachments | Kranich #15 |
| Line item with extended description | Kranich #13 |
| Many line items on one invoice | Kranich #14 (10 line items) |
| Customer with multiple mail recipients | Müller Maschinenbau, Acme Robotics |
| Recurring — Daily | MK-6 (Studio Bellini) |
| Recurring — Weekly | MK-5 (Holzbau Eichenhain, Draft) |
| Recurring — Monthly | MK-1 (Müller), MN-1 (Sunfire) |
| Recurring — Quarterly | MK-2 (Pixelblut), MN-2 (Halcyon, Paused) |
| Recurring — Half-Yearly | MK-3 (Acme) |
| Recurring status: Draft | MK-5 |
| Recurring status: Active | MK-1, MK-2, MK-3, MK-6, MN-1 |
| Recurring status: Paused | MK-4 (Stadtkultur), MN-2 (Halcyon) |
| Recurring → generated Invoice link | MK-1 → Kranich invoice #6 |

## What this seeder does NOT contain

Deliberate omissions, so you know not to look for them:

- **Faker-random padding invoices** — every invoice has a story. If you want volume-testing data, that's a separate seeder.
- **Idempotent re-seeding** — designed for `make fresh`, not for re-running on a live DB.
- **Old `BaltStaffSeeder` style hardcoded passwords** — removed for security reasons; passwords now come from env vars or are generated at seed time.
