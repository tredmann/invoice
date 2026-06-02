# Rename: Database Columns

## Goal
Rename six database columns to match the ubiquitous language in `CONTEXT.md`. Each rename requires a migration plus updates to every PHP file, Blade template, test, and API resource that references the column.

| Old name | New name | Tables affected |
|---|---|---|
| `performed_when` | `service_period` | `invoices` |
| `invoice_no` | `invoice_number` | `invoices` |
| `days_till_due` | `payment_terms` | `invoices`, `master_invoices` |
| `tax_rate` | `vat_rate` | `line_items`, `master_line_items` |
| `detail` | `description` | `line_items`, `master_line_items` |
| `detail_plus` | `extended_description` | `line_items`, `master_line_items` |

## Do first
Docs 01, 02, and 03 should be complete first so model class names are already stable.

## Warning: `description` is a reserved word in some contexts
The column name `description` is safe in MySQL/PostgreSQL but check that Laravel's query builder or any raw SQL doesn't conflict. If there is a conflict, use `line_description` instead.

---

## For each rename: the pattern

Every column rename follows this pattern:
1. **Write a migration** using `Schema::table()` with `->renameColumn()`
2. **Update the model** `$fillable`, `@property` docblock, `$casts` if present
3. **Update all PHP files** that reference the column by name
4. **Update all Blade views** that reference the column
5. **Update API resources** (V1 camelCase and V2 snake_case keys)
6. **Update request validation** rules (field name keys)
7. **Update language files** (`attributes.php` and any translation keys)
8. **Update tests**

---

## 1. `performed_when` → `service_period`

### Migration
```php
Schema::table('invoices', function (Blueprint $table) {
    $table->renameColumn('performed_when', 'service_period');
});
```

### Model: `Invoice.php`
- `@property string $performed_when` → `@property string $service_period`
- `'performed_when'` in `$fillable` → `'service_period'`

### PHP files
- `src/app/Services/Invoices/InvoiceService.php` (lines 37, 100) — `'performed_when' => ...` → `'service_period' => ...`
- `src/app/Services/API/V2/InvoiceService.php` — same
- `src/app/Jobs/SubscriptionHandler.php` (line 30) — `'performed_when' => $this->buildPerformedWhen()` → `'service_period' => ...`
- `src/app/Models/MasterInvoice.php` — rename method `buildPerformedWhen()` → `buildServicePeriod()`; update the call in SubscriptionHandler
- `src/app/Http/Requests/InvoiceStatusOpenRequest.php` (line 30) — validation key `'performed_when'` → `'service_period'`
- `src/app/Http/Requests/API/V2/InvoiceRequest.php` (line 35) — same
- `src/database/factories/InvoiceFactory.php` (lines 45, 64) — key rename

### API resources
- `src/app/Http/Resources/V1/InvoiceResource.php` — `'performedWhen' => $this->performed_when` → `'servicePeriod' => $this->service_period`
- `src/app/Http/Resources/V2/InvoiceResource.php` — `'performed_when' => $this->performed_when` → `'service_period' => $this->service_period`

### Language files
- `src/resources/lang/de/attributes.php` — key `'performed_when'` → `'service_period'`

### Views
- `src/resources/views/default/invoices/conclude.blade.php` (lines 30-35) — `name="performed_when"`, `id="performed_when"`, `old('performed_when')` → `service_period`
- `src/resources/views/default/invoices/invoice-pdf.blade.php` (line 131) — `$invoice->performed_when` → `$invoice->service_period`
- `src/resources/views/default/invoices/cancelled-invoice-pdf.blade.php` (line 131) — same
- `src/resources/views/default/invoices/components/invoicesShowTable.blade.php` (lines 14, 17) — same

### Tests
- `src/tests/Feature/Http/Controllers/InvoiceControllerTest.php` (lines 75, 103, 139, 164)
- `src/tests/Feature/Http/Controllers/API/V1/InvoiceControllerTest.php` (lines 61, 91)
- `src/tests/Feature/Http/Controllers/API/V2/InvoiceControllerTest.php` (lines 143, 282)

---

## 2. `invoice_no` → `invoice_number`

### Migration
```php
Schema::table('invoices', function (Blueprint $table) {
    $table->renameColumn('invoice_no', 'invoice_number');
});
```

### Model: `Invoice.php`
- `@property string $invoice_no` → `@property string $invoice_number`
- `'invoice_no'` in `$fillable` → `'invoice_number'`
- `getInvoiceNumber()` method — update body: `return $this->invoice_no;` → `return $this->invoice_number;`

### PHP files
- `src/app/Services/Invoices/InvoiceService.php` (lines 38, 101, 168) — `'invoice_no' => UniqueNumber::...` → `'invoice_number' => ...`
- `src/app/Services/API/V2/InvoiceService.php` (line 48) — same
- `src/app/Http/Controllers/CustomerController.php` (line 41) — `orderBy('invoice_no')` → `orderBy('invoice_number')`
- `src/database/factories/InvoiceFactory.php` (lines 44, 60) — key rename

### API resources
- `src/app/Http/Resources/V1/InvoiceResource.php` — `'invoiceNo' => $this->invoice_no` → `'invoiceNumber' => $this->invoice_number`
- `src/app/Http/Resources/V2/InvoiceResource.php` — `'invoice_no' => $this->invoice_no` → `'invoice_number' => $this->invoice_number`

### Language files
- `src/resources/lang/de/attributes.php` — key `'invoice_no'` → `'invoice_number'`
- `src/resources/lang/de/invoicePdf.php` (line 5) — any `invoice_no` reference
- `src/resources/lang/de/invoices.php` (lines 24, 44, 58, 65) — keys containing `invoice_no`
- `src/resources/lang/en/invoicePdf.php` (line 5) — same
- `src/resources/lang/en/invoices.php` (lines 55, 68) — same

### Views (extensive — check all)
- `src/resources/views/default/invoices/invoice-pdf.blade.php` (lines 122, 128, 230)
- `src/resources/views/default/invoices/cancelled-invoice-pdf.blade.php` (lines 122, 128)
- `src/resources/views/default/invoices/components/invoicesList.blade.php` (lines 9, 60)
- `src/resources/views/default/invoices/components/invoicesShowTable.blade.php` (lines 6, 9)
- `src/resources/views/default/customers/components/customersInvoicesTable.blade.php` (lines 11, 85)
- `src/resources/views/dashboard/components/dashboardOpenInvoicesTable.blade.php` (lines 9, 24)
- `src/resources/views/dashboard/components/dashboardOverdueInvoicesTable.blade.php` (lines 9, 24)
- `src/resources/views/dashboard/components/dashboardPaidInvoicesTable.blade.php` (lines 9, 24)
- `src/resources/views/default/emails/invoice-mail.blade.php` (lines 14, 16)
- `src/resources/views/default/emails/invoice-cancellation-mail.blade.php` (line 14)
- `src/resources/views/goerzwerk/pdf/invoice.blade.php` (lines 144, 151, 247)
- `src/resources/views/goerzwerk/pdf/invoice-cancellation.blade.php` (lines 143, 150)
- `src/resources/views/goerzwerk/mail/invoice.blade.php` (lines 12, 16)
- `src/resources/views/goerzwerk/mail/invoice-cancellation.blade.php` (line 8)
- `src/routes/breadcrumbs.php` (lines 148-160)

---

## 3. `days_till_due` → `payment_terms`

### Migration
```php
Schema::table('invoices', function (Blueprint $table) {
    $table->renameColumn('days_till_due', 'payment_terms');
});
Schema::table('master_invoices', function (Blueprint $table) {
    $table->renameColumn('days_till_due', 'payment_terms');
});
```

### Models
**`Invoice.php`:**
- `@property int $days_till_due` → `@property int $payment_terms`
- `'days_till_due'` in `$fillable` → `'payment_terms'`
- Rename constant: `DAYS_TILL_DUE` → `PAYMENT_TERMS` (update all references)

**`MasterInvoice.php` (→ `RecurringInvoice.php` after doc 02):**
- Same as Invoice — `@property`, `$fillable`, and `DAYS_TILL_DUE` → `PAYMENT_TERMS`

### PHP files
- `src/app/Services/Invoices/InvoiceService.php` (lines 36, 99) — `$attributes['days_till_due']` → `$attributes['payment_terms']`
- `src/app/Services/API/V2/InvoiceService.php` (line 47) — same
- `src/app/Services/MasterInvoices/MasterInvoiceService.php` (line 73) — same
- `src/app/Jobs/SubscriptionHandler.php` (line 29) — `'days_till_due' => ...` → `'payment_terms' => ...`
- `src/app/Http/Requests/InvoiceStatusOpenRequest.php` (line 29) — validation key
- `src/app/Http/Requests/MasterInvoiceActiveRequest.php` (line 25) — validation key
- `src/app/Http/Requests/API/V2/InvoiceRequest.php` (line 34) — validation key
- `src/database/factories/InvoiceFactory.php` (lines 46, 65-66)
- `src/database/factories/MasterInvoiceFactory.php` (lines 38, 55-56)

### API resources
- `src/app/Http/Resources/V1/InvoiceResource.php` — `'daysTillDue' => $this->days_till_due` → `'paymentTerms' => $this->payment_terms`
- `src/app/Http/Resources/V1/MasterInvoiceResource.php` — same
- `src/app/Http/Resources/V2/InvoiceResource.php` — `'days_till_due'` → `'payment_terms'`

### Language files
- `src/resources/lang/de/attributes.php` — key `'days_till_due'` → `'payment_terms'`
- `src/resources/lang/de/invoices.php` — keys `'days_till_due.7'`, `'days_till_due.14'`, `'days_till_due.30'` → `'payment_terms.7'`, etc.
- `src/resources/lang/de/masterInvoices.php` — same pattern
- `src/resources/lang/en/invoices.php` — same
- `src/resources/lang/en/masterInvoices.php` — same

### Views
- `src/resources/views/default/invoices/conclude.blade.php` (lines 21-24)
- `src/resources/views/default/invoices/components/invoicesShowTable.blade.php` (lines 22, 25)
- `src/resources/views/default/master_invoices/activate.blade.php` (lines 20-23, 29)

### Tests — all test files referencing `days_till_due`

---

## 4. `tax_rate` → `vat_rate`

### Migration
```php
Schema::table('line_items', function (Blueprint $table) {
    $table->renameColumn('tax_rate', 'vat_rate');
});
Schema::table('master_line_items', function (Blueprint $table) {
    $table->renameColumn('tax_rate', 'vat_rate');
});
```

### Models
**`LineItem.php`:** `@property float $tax_rate` → `@property float $vat_rate`; `'tax_rate'` in `$fillable` → `'vat_rate'`
**`MasterLineItem.php`:** same

### PHP files
- `src/app/Services/Shared/LineItemProcessorService.php` (line 13) — `$data['tax_rate']` → `$data['vat_rate']`
- `src/app/Services/Invoices/InvoiceService.php` (lines 146-150) — `'tax_rate'` grouping key
- `src/app/Services/MasterInvoices/MasterInvoiceService.php` (line 86) — `'tax_rate' => ...` → `'vat_rate' => ...`
- `src/app/Models/Invoice.php` (line 169) — `$lineItem->tax_rate` in `getTaxDistribution()`; also update `taxKey` logic
- `src/app/Http/Requests/LineItemRequest.php` (line 38) — validation key
- `src/app/Http/Requests/MasterLineItemRequest.php` (line 32) — validation key
- `src/app/Http/Requests/API/V2/InvoiceRequest.php` (line 39) — validation key
- `src/database/factories/LineItemFactory.php` (lines 34, 36)
- `src/database/factories/MasterLineItemFactory.php` (lines 30-31)

### API resources
- `src/app/Http/Resources/V1/LineItemResource.php` — `'taxRate' => $this->tax_rate` → `'vatRate' => $this->vat_rate`
- `src/app/Http/Resources/V1/MasterLineItemResource.php` — `'tax_rate'` → `'vat_rate'`
- `src/app/Http/Resources/V2/LineItemResource.php` — `'tax_rate'` → `'vat_rate'`

### Language files
- `src/resources/lang/de/attributes.php` — `'tax_rate'` → `'vat_rate'`

### Views
- `src/resources/views/default/line_items/create.blade.php` (lines 77-82)
- `src/resources/views/default/line_items/edit.blade.php` (lines 64-69)
- `src/resources/views/default/master_line_items/create.blade.php` (lines 80-85)
- `src/resources/views/default/master_line_items/edit.blade.php` (lines 64-69)
- Any PDF templates that render tax rates per line item

---

## 5. `detail` → `description` and `detail_plus` → `extended_description`

### Migration
```php
Schema::table('line_items', function (Blueprint $table) {
    $table->renameColumn('detail', 'description');
    $table->renameColumn('detail_plus', 'extended_description');
});
Schema::table('master_line_items', function (Blueprint $table) {
    $table->renameColumn('detail', 'description');
    $table->renameColumn('detail_plus', 'extended_description');
});
```

### Models
**`LineItem.php`:**
- `@property string $detail` → `@property string $description`
- `@property string $detail_plus` → `@property string|null $extended_description`
- Update `$fillable`

**`MasterLineItem.php`:** same

### PHP files
- `src/app/Services/MasterInvoices/MasterInvoiceService.php` (lines 89-90) — `'detail' => ...`, `'detail_plus' => ...` → new names
- `src/app/Http/Requests/LineItemRequest.php` (lines 40-41) — validation keys
- `src/app/Http/Requests/MasterLineItemRequest.php` (lines 34-35) — validation keys
- `src/app/Http/Requests/API/V2/InvoiceRequest.php` (lines 41-42) — validation keys
- `src/database/factories/LineItemFactory.php` (lines 38-39)
- `src/database/factories/MasterLineItemFactory.php` (lines 33-34)

### API resources
- `src/app/Http/Resources/V1/LineItemResource.php` — `'detail' => $this->detail` → `'description' => $this->description`; `'detailPlus' => $this->detail_plus` → `'extendedDescription' => $this->extended_description`
- `src/app/Http/Resources/V1/MasterLineItemResource.php` — same with snake_case
- `src/app/Http/Resources/V2/LineItemResource.php` — `'detail'` → `'description'`; `'detail_plus'` → `'extended_description'`

### Language files
- `src/resources/lang/de/attributes.php` — `'detail' => 'Überschrift'` → `'description' => 'Überschrift'`; `'detail_plus' => 'Text'` → `'extended_description' => 'Text'`

### Views
- `src/resources/views/default/line_items/edit.blade.php` (lines 89-106)
- `src/resources/views/default/master_line_items/create.blade.php`
- `src/resources/views/default/master_line_items/edit.blade.php`
- `src/resources/views/default/invoices/components/invoicesLineItemsTable.blade.php` (line 34) — `$lineItem->detail_plus` → `$lineItem->extended_description`
- `src/resources/views/default/invoices/invoice-pdf.blade.php` (line 158)
- `src/resources/views/default/invoices/cancelled-invoice-pdf.blade.php` (line 158)
- `src/resources/views/goerzwerk/pdf/invoice.blade.php` (line 174)
- `src/resources/views/goerzwerk/pdf/invoice-cancellation.blade.php` (line 173)

---

## Verification
After all changes:
1. `grep -r "performed_when\|invoice_no\|days_till_due\|tax_rate\|detail_plus\b" src/ --include="*.php"` — should return zero results
2. `grep -r "\bdetail\b" src/app/Models/ --include="*.php"` — zero results (careful: `detail` is a common word, scope narrowly)
3. Run migrations in test environment: `php artisan migrate`
4. Run the full test suite
