# Ubiquitous Language

## Company
The business entity that issues invoices. Has a name, address, legal registration details, and banking information. Owns Customers and all invoicing activity. Called `Tenant` in the code.

## User
A person who can log in and act on behalf of a Company. Every Company has one designated Owner (a User with elevated privileges), but all are referred to as Users in general.

## Recurring Invoice
A template that automatically generates Invoices for a Customer on a fixed Billing Frequency. Has its own lifecycle: Draft → Active → Paused. Tracks the next generation date. Called `MasterInvoice` in the code.

## Billing Frequency
The interval at which a Recurring Invoice generates a new Invoice. Fixed set: Daily, Weekly, Monthly, Quarterly (3 months), Half-Yearly (6 months). Stored as an interval string in the code (`1 day`, `1 week`, `1 month`, `3 months`, `6 months`).

## Invoice
A billing document issued by a Company to a Customer. Has a unique invoice number, a Service Period, Line Items, and a due date.

**Lifecycle:** Draft → Open → Paid or Overdue → (Cancelled)

- **Draft**: being composed, not yet issued
- **Open**: issued with an invoice number and due date assigned
- **Paid**: Customer has paid
- **Overdue**: due date has passed without payment; transition is currently triggered manually
- **Cancelled**: voided; always paired with a Cancellation Invoice
- **PDF Generation Error**: technical state — Invoice was opened but PDF generation failed; not a business lifecycle step. Called `open pdf error` in the code.

**Mail Status** (independent of lifecycle): tracks whether the Invoice has been emailed to the Customer.
- **Not Queued**: email not requested (default; Cancellation Invoices start here). Called `not mailable` in the code.
- **Queued**: User has requested the Invoice to be emailed. Called `mailable` in the code.
- **Sending**: email is being dispatched. Called `mailing` in the code.
- **Sent**: email was delivered successfully. Called `mailed` in the code.
- **Send Error**: email dispatch failed. Called `mail error` in the code.

## Cancelled Invoice
An Invoice that has been voided. A Cancellation Invoice is always created alongside it. Status: `cancelled` in the code.

## Cancellation Invoice
A legally distinct reversal document that negates a Cancelled Invoice. Carries its own invoice number. References the original Invoice via `cancelled_invoice_id` in the code. Status: `cancellation invoice` in the code.

## Customer Number
A human-readable identifier assigned to a Customer, unique within a Company. Used to reference Customers in conversation and UI. Called `customer_no` in the code.

## Payment Terms
The number of days a Customer has to pay an Invoice after it is opened. Valid values: 7, 14, or 30 days. Called `days_till_due` in the code.

## Due Date
The calculated date by which an Invoice must be paid. Derived from the open date plus the Payment Terms. An Invoice becomes Overdue when this date passes without payment. Called `date_due` in the code.

## Invoice Number
A unique identifier assigned to an Invoice or Cancellation Invoice when it is opened. Generated from a single shared sequence — Cancellation Invoices draw from the same sequence as regular Invoices. Called `invoice_no` in the code.

## VAT Rate
The value-added tax percentage applied to a Line Item. Stored as a float (e.g. `0.19` for 19%). Valid values: 0% (tax-exempt), 7% (reduced), 19% (standard German VAT). Called `tax_rate` in the code.

## Amount
A monetary value, always stored as an integer in the smallest currency unit (cents). Formatted for display using the `Money` value object in the code, which divides by 100 and applies locale-specific currency formatting. Supported currencies: EUR, USD.

## Invoice Template
The visual layout used to render an Invoice as a PDF. Currently developer-managed — not configurable by Users. A Company uses a specific Invoice Template selected at the tenant level. Called `BladeInvoiceTemplate` in the code.

## DATEV Export
A data export in the format required by DATEV accounting software. Used by German tax accountants to process invoice data. A first-class feature that Users interact with directly.

## Company Settings
Key/value configuration options scoped to a Company. Only the Owner can configure them. Examples include default currency and email preferences. Called `Setting` in the code.

## Company Profile
The general contact information of a Company: name, address, email, phone, fax, and homepage. Printed on invoices as the issuer's identity. Versioned — a Company points to its current profile. Called `GeneralInfo` in the code.

## Company Legal Info
The legal and banking details of a Company: registry court, registry number, tax number, VAT number, IBAN, BIC, and bank name. Required for legally compliant invoices. Versioned — a Company points to its current legal info. Called `LegalInfo` in the code.

## Invoice Document
The generated PDF rendering of an Invoice, stored as the authoritative version. A type of file associated with an Invoice. Called `InvoiceDocument` with type `invoice_document` in the code.

## ZUGFeRD Document
A PDF/A-3 file that embeds a structured ZUGFeRD-compliant XML payload alongside the human-readable Invoice rendering. Satisfies German and EU electronic invoicing requirements (EN 16931). When a ZUGFeRD Document exists for an Invoice, it supersedes the plain Invoice Document as the file sent to Customers and used for accounting export.

## Attachment
An additional file associated with an Invoice, sent alongside the Invoice Document when emailing. A type of file associated with an Invoice. Called `InvoiceDocument` with type `attachment` in the code.

## Service Period
A human-readable description of when the billed service was delivered (e.g. "June 2026", "KW 23 2026"). Free-form string on an Invoice. Generated automatically from the billing schedule on a Recurring Invoice. Called `performed_when` in the code.

## Line Item
A single billable entry on an Invoice or Recurring Invoice. Has a quantity, unit, unit price, tax rate, Description, and optional Extended Description. Called `LineItem` (on Invoice) and `MasterLineItem` (on Recurring Invoice) in the code. The `detail` field is the Description; `detail_plus` is the Extended Description.

## Customer
A person or business that receives invoices from a Company. Has a name, optional company name, and a postal address. Called `Customer` in the code.

## Mail Recipient
An email address associated with a Customer, used to deliver invoices by email. A Customer can have multiple Mail Recipients. Called `CustomerMailReceiver` in the code.
