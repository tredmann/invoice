# Rename: Mail Status Constants and Values

## Goal
Rename the `mail_status` constants and their stored string values on `Invoice` to match the ubiquitous language in `CONTEXT.md`. The current values (`not mailable`, `mailable`, etc.) are implementation-speak. The domain terms are: **Not Queued**, **Queued**, **Sending**, **Sent**, **Send Error**.

## Do first
Docs 01–03 should be complete first so the `Invoice` model class name is stable.

## Scope
This rename has two layers:
1. **PHP constant names** — rename the `MAIL_STATUS_*` constants on `Invoice`
2. **Stored string values** — change the actual strings stored in the database (requires a data migration)

Both layers must be done together, or the code will not match the data.

---

## 1. Constant renames on `Invoice` model
**File:** `src/app/Models/Invoice.php`

| Old constant name | New constant name | Old value | New value |
|---|---|---|---|
| `MAIL_STATUS_NOT_MAILABLE` | `MAIL_STATUS_NOT_QUEUED` | `'not mailable'` | `'not_queued'` |
| `MAIL_STATUS_MAILABLE` | `MAIL_STATUS_QUEUED` | `'mailable'` | `'queued'` |
| `MAIL_STATUS_MAILING` | `MAIL_STATUS_SENDING` | `'mailing'` | `'sending'` |
| `MAIL_STATUS_MAILED` | `MAIL_STATUS_SENT` | `'mailed'` | `'sent'` |
| `MAIL_STATUS_ERROR` | `MAIL_STATUS_SEND_ERROR` | `'mail error'` | `'send_error'` |

---

## 2. Data migration
Create a migration that updates all existing rows in the `invoices` table:

```php
DB::table('invoices')->where('mail_status', 'not mailable')->update(['mail_status' => 'not_queued']);
DB::table('invoices')->where('mail_status', 'mailable')->update(['mail_status' => 'queued']);
DB::table('invoices')->where('mail_status', 'mailing')->update(['mail_status' => 'sending']);
DB::table('invoices')->where('mail_status', 'mailed')->update(['mail_status' => 'sent']);
DB::table('invoices')->where('mail_status', 'mail error')->update(['mail_status' => 'send_error']);
```

Include a `down()` method that reverses these changes (swap old/new values).

---

## 3. Update all PHP files referencing the old constant names

Replace every use of the old constant names with the new ones:

**`src/app/Actions/Invoice/OpenInvoiceAction.php`**
- `Invoice::MAIL_STATUS_MAILABLE` → `Invoice::MAIL_STATUS_QUEUED`

**`src/app/Actions/Invoice/SendMailAction.php`**
- `Invoice::MAIL_STATUS_ERROR` → `Invoice::MAIL_STATUS_SEND_ERROR`
- `Invoice::MAIL_STATUS_MAILABLE` → `Invoice::MAIL_STATUS_QUEUED`

**`src/app/Jobs/SendInvoiceMail.php`**
- `Invoice::MAIL_STATUS_MAILED` → `Invoice::MAIL_STATUS_SENT`
- `Invoice::MAIL_STATUS_ERROR` → `Invoice::MAIL_STATUS_SEND_ERROR`

**`src/app/Jobs/SendCancellationInvoiceMail.php`**
- `Invoice::MAIL_STATUS_MAILED` → `Invoice::MAIL_STATUS_SENT`
- `Invoice::MAIL_STATUS_ERROR` → `Invoice::MAIL_STATUS_SEND_ERROR`

**`src/app/Jobs/GeneratePDF.php`**
- `Invoice::MAIL_STATUS_MAILABLE` → `Invoice::MAIL_STATUS_QUEUED`

**`src/app/Services/Invoices/InvoiceService.php`**
- Any direct string comparisons or constant usages

**`src/app/Services/API/V2/InvoiceService.php`**
- Any direct string comparisons or constant usages

Check all other PHP files:
```
grep -r "MAIL_STATUS_\|mail_status" src/app --include="*.php"
```

---

## 4. Update hardcoded string comparisons
Some code may compare `mail_status` to the raw string values rather than using constants. Find and replace:

```
grep -r "'not mailable'\|'mailable'\|'mailing'\|'mailed'\|'mail error'" src/ --include="*.php"
```

Replace each with the corresponding constant reference (`Invoice::MAIL_STATUS_NOT_QUEUED`, etc.).

---

## 5. Language files
**`src/resources/lang/de/invoices.php`**

Update translation keys (the key is the stored value, used as `__('invoices.mail_status.' . $invoice->mail_status)`):
```php
// Old
'mail_status.not mailable' => 'Nicht bereit',
'mail_status.mailable'     => 'Bereit',
'mail_status.mailing'      => 'Wird verschickt',
'mail_status.mailed'       => 'Verschickt',
'mail_status.mail error'   => 'Emailfehler!',

// New
'mail_status.not_queued'   => 'Nicht bereit',
'mail_status.queued'       => 'Bereit',
'mail_status.sending'      => 'Wird verschickt',
'mail_status.sent'         => 'Verschickt',
'mail_status.send_error'   => 'Emailfehler!',
```

**`src/resources/lang/en/invoices.php`** — same key pattern:
```php
'mail_status.not_queued'   => 'Not queued',
'mail_status.queued'       => 'Queued',
'mail_status.sending'      => 'Sending',
'mail_status.sent'         => 'Sent',
'mail_status.send_error'   => 'Send error',
```

---

## 6. Blade views
Views that display `mail_status` typically do so via `__('invoices.mail_status.' . $invoice->mail_status)`. No changes needed in the views themselves if the language file keys are updated correctly (step 5).

However, verify there are no hardcoded string comparisons in Blade files:
```
grep -r "mail_status\|mailable\|mailing\|mailed" src/resources/views --include="*.blade.php"
```

---

## 7. Tests
Search for all test files that assert `mail_status` values and update the expected strings:
```
grep -r "not mailable\|mailable\|mailing\|mailed\|mail error" src/tests --include="*.php"
```

Also update any assertions using the old constant names:
```
grep -r "MAIL_STATUS_" src/tests --include="*.php"
```

---

## Verification
After all changes:
1. `grep -r "MAIL_STATUS_NOT_MAILABLE\|MAIL_STATUS_MAILABLE\|MAIL_STATUS_MAILING\|MAIL_STATUS_MAILED\|MAIL_STATUS_ERROR" src/ --include="*.php"` — zero results
2. `grep -r "'not mailable'\|'mailable'\|'mailing'\|'mailed'\|'mail error'" src/ --include="*.php"` — zero results
3. Run migrations: `php artisan migrate`
4. Verify the database has no rows with old values: `SELECT DISTINCT mail_status FROM invoices;` — should only show new values
5. Run the full test suite
