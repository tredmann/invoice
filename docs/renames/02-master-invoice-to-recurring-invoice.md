# Rename: MasterInvoice → RecurringInvoice & MasterLineItem → RecurringLineItem

## Goal
Rename `MasterInvoice` to `RecurringInvoice` and `MasterLineItem` to `RecurringLineItem` throughout the codebase, aligning with the ubiquitous language in `CONTEXT.md`. A "MasterInvoice" is a template that generates Invoices on a recurring schedule — it is called a **Recurring Invoice**. Its line items are **Recurring Line Items** (but called **Line Item** in domain conversation; the `RecurringLineItem` class name is a code-level disambiguation only).

## Do first
None — this document has no prerequisites.

## Do NOT touch in this pass
- Database table names `master_invoices` and `master_line_items` (no DB schema changes)
- Foreign key column `master_invoice_id` in the `master_line_items` table

---

## 1. Model: `MasterInvoice`
**File:** `src/app/Models/MasterInvoice.php`
- Rename file to `RecurringInvoice.php`
- Rename class: `class MasterInvoice` → `class RecurringInvoice`
- Rename relationship method: `masterLineItems()` → `recurringLineItems()`
- Update status constants (names only, not values — values are handled in a separate concern):
  - `STATUS_DRAFT`, `STATUS_ACTIVE`, `STATUS_PAUSED` — names unchanged, keep as-is
- Update `DAYS_TILL_DUE` constant — keep as-is
- Update `BILLING_FREQUENCIES` and billing constant names — keep as-is
- Rename method: `buildPerformedWhen()` — keep as-is (field name handled in doc 04)
- Update `protected $table` if explicit — set to `'master_invoices'` (add if not present, to preserve table name)

## 2. Model: `MasterLineItem`
**File:** `src/app/Models/MasterLineItem.php`
- Rename file to `RecurringLineItem.php`
- Rename class: `class MasterLineItem` → `class RecurringLineItem`
- Rename relationship method: `masterInvoice()` → `recurringInvoice()`
- Update `boot()` static closures: `MasterInvoiceService::` → `RecurringInvoiceService::` (once service is renamed)
- Add `protected $table = 'master_line_items';` if not already present

## 3. Update all imports across the codebase
Replace every:
```php
use App\Models\MasterInvoice;
```
→
```php
use App\Models\RecurringInvoice;
```

Replace every:
```php
use App\Models\MasterLineItem;
```
→
```php
use App\Models\RecurringLineItem;
```

**Files to update:**
- `src/app/Models/Customer.php` — import + relationships `masterInvoices()` → `recurringInvoices()`, `hasMany(MasterInvoice::class)` → `hasMany(RecurringInvoice::class)`
- `src/app/Models/Tenant/Tenant.php` — import + `masterInvoices()` → `recurringInvoices()`
- `src/app/Http/Controllers/MasterInvoiceController.php`
- `src/app/Http/Controllers/API/V1/MasterInvoiceController.php`
- `src/app/Services/MasterInvoices/MasterInvoiceService.php`
- `src/app/Services/MasterLineItems/MasterLineItemService.php`
- `src/app/Jobs/SubscriptionHandler.php`
- `src/app/Http/Requests/MasterInvoiceStoreRequest.php`
- `src/app/Http/Requests/MasterInvoiceActiveRequest.php`
- `src/app/Http/Requests/MasterLineItemRequest.php`
- `src/app/Http/Resources/V1/MasterInvoiceResource.php`
- `src/app/Http/Resources/V1/MasterLineItemResource.php`
- `src/database/factories/MasterInvoiceFactory.php`
- `src/database/factories/MasterLineItemFactory.php`
- `src/database/seeders/MasterInvoiceActiveSeeder.php`
- `src/database/seeders/MasterInvoiceDraftSeeder.php`
- `src/database/seeders/MasterInvoicePausedSeeder.php`
- `src/database/seeders/MasterLineItemSeeder.php`
- All test files under `src/tests/` referencing `MasterInvoice` or `MasterLineItem`

## 4. Controller file renames
- `src/app/Http/Controllers/MasterInvoiceController.php` → `RecurringInvoiceController.php` (class: `MasterInvoiceController` → `RecurringInvoiceController`)
- `src/app/Http/Controllers/API/V1/MasterInvoiceController.php` → `RecurringInvoiceController.php`
- `src/app/Http/Controllers/MasterLineItemController.php` → `RecurringLineItemController.php`
- `src/app/Http/Controllers/API/V1/MasterLineItemController.php` → `RecurringLineItemController.php`

## 5. Service file renames
- `src/app/Services/MasterInvoices/` directory → `RecurringInvoices/`
- `src/app/Services/MasterInvoices/MasterInvoiceService.php` → `RecurringInvoices/RecurringInvoiceService.php` (class: `MasterInvoiceService` → `RecurringInvoiceService`)
- `src/app/Services/MasterLineItems/` directory → `RecurringLineItems/`
- `src/app/Services/MasterLineItems/MasterLineItemService.php` → `RecurringLineItems/RecurringLineItemService.php` (class: `MasterLineItemService` → `RecurringLineItemService`)

## 6. Request file renames
- `MasterInvoiceStoreRequest.php` → `RecurringInvoiceStoreRequest.php`
- `MasterInvoiceActiveRequest.php` → `RecurringInvoiceActiveRequest.php`
- `MasterLineItemRequest.php` → `RecurringLineItemRequest.php`

## 7. Resource file renames
- `src/app/Http/Resources/V1/MasterInvoiceResource.php` → `RecurringInvoiceResource.php`
- `src/app/Http/Resources/V1/MasterLineItemResource.php` → `RecurringLineItemResource.php`

## 8. Factory and seeder file renames
- `MasterInvoiceFactory.php` → `RecurringInvoiceFactory.php`
- `MasterLineItemFactory.php` → `RecurringLineItemFactory.php`
- `MasterInvoiceActiveSeeder.php` → `RecurringInvoiceActiveSeeder.php`
- `MasterInvoiceDraftSeeder.php` → `RecurringInvoiceDraftSeeder.php`
- `MasterInvoicePausedSeeder.php` → `RecurringInvoicePausedSeeder.php`
- `MasterLineItemSeeder.php` → `RecurringLineItemSeeder.php`

## 9. Route files
**`src/routes/web.php`**
- Update route group names: `masterInvoices` → `recurringInvoices`, `masterLineItems` → `recurringLineItems`
- Update controller references to renamed controller classes
- Update route names (e.g. `masterInvoices.create` → `recurringInvoices.create`) — **note: this is a breaking change if frontend hardcodes route names**

**`src/routes/api_V1.php`**
- Same controller + route name updates

**`src/routes/breadcrumbs.php`**
- Update all breadcrumb definitions referencing `masterInvoices` or `masterLineItems`

## 10. Language files
- Rename `src/resources/lang/de/masterInvoices.php` → `recurringInvoices.php`
- Rename `src/resources/lang/en/masterInvoices.php` → `recurringInvoices.php`
- Rename `src/resources/lang/de/masterLineItems.php` → `recurringLineItems.php`
- Rename `src/resources/lang/en/masterLineItems.php` → `recurringLineItems.php`
- Update all `__('masterInvoices.*')` and `__('masterLineItems.*')` calls in views/controllers

## 11. Views
- Rename directory `src/resources/views/default/master_invoices/` → `recurring_invoices/`
- Rename directory `src/resources/views/default/master_line_items/` → `recurring_line_items/`
- Rename files within:
  - `masterInvoicesMasterLineItemsTable.blade.php` → `recurringInvoicesRecurringLineItemsTable.blade.php`
  - `masterLineItems.blade.php` → `recurringLineItems.blade.php`
- Update all `view('default.master_invoices.*')` and `view('default.master_line_items.*')` references

## 12. Config
- `src/config/unique-numbers.php` — check for `MasterInvoice::class` key and update to `RecurringInvoice::class`

---

## Verification
After all changes:
1. `grep -r "MasterInvoice" src/ --include="*.php"` — should return zero results
2. `grep -r "MasterLineItem" src/ --include="*.php"` — should return zero results
3. `grep -r "masterInvoices\|masterLineItems" src/resources/ --include="*.blade.php"` — should return zero results
4. Run `php artisan route:list` — should produce no errors
5. Run the full test suite
