# ZUGFeRD Documents are mandatory for all Companies at EN 16931 profile

Every Invoice Document is a ZUGFeRD Document (PDF/A-3 with embedded EN 16931 XML), generated via `horstoeko/zugferd`. There is no per-Company opt-in.

## Considered Options

**Opt-in per Company Setting** was rejected. The German B2BG mandate (mandatory e-invoicing for B2B from 2025/2027) will eventually apply to every Company using this app. An opt-in toggle buys a short-term simplification at the cost of a later migration to flip everyone on — and during that window, Companies subject to the mandate could unknowingly send non-compliant invoices.

**BASIC or MINIMUM profile** was rejected in favour of EN 16931. DATEV, Lexware, and most German accounting software expect EN 16931 (Comfort). Lower profiles omit line-item detail that accountants need; generating them would produce technically valid but practically useless ZUGFeRD Documents.

**Manual XML generation** was rejected in favour of `horstoeko/zugferd`. EN 16931 has ~80 required fields with strict CII schema validation. Maintaining hand-rolled XML would be a permanent liability; `horstoeko/zugferd` is the de facto PHP standard for this.

## Consequences

- Every Company must have `vat_no`, `tax_no`, `iban`, and `swift_bic` filled in `LegalInfo` before opening an Invoice. Missing fields cause generation to fail (→ `PDF Generation Error`). A future setup step should gate this at onboarding.
- Existing Invoice Documents (plain PDFs) are never regenerated — immutable by policy.
- `InvoiceDocument` type constants and storage paths are unchanged; the ZUGFeRD PDF is stored as the `invoice_document` type, superseding the plain PDF transparently.
