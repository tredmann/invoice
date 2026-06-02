# Rename: Tenant → Company

## Goal
Rename the `Tenant` concept throughout the codebase to `Company`, aligning with the ubiquitous language defined in `CONTEXT.md`. In the domain, a "Tenant" is the business entity that issues invoices — it is called a **Company**.

## Do first
None — this document has no prerequisites.

## Do NOT touch in this pass
- The database table name `tenants` (keep as-is; rename separately if ever needed)
- The namespace `App\Models\Tenant\` (keep as-is to limit blast radius)
- The `TracksTenant` trait name (minor, low value)
- Foreign key column names like `tenant_id` in the database

## Scope
Rename at the PHP class / method / variable / route / view / language level only. No DB schema changes.

---

## 1. Model: `App\Models\Tenant\Tenant`
**File:** `src/app/Models/Tenant/Tenant.php`
- Rename class: `class Tenant` → `class Company`
- Rename relationship method: `masterInvoices()` stays as-is (handled in doc 02)
- Update docblock `@property` type references where they say `Tenant`
- Keep file path as-is (`Tenant/Tenant.php`) — do not move the file

## 2. All `use App\Models\Tenant\Tenant` imports
Replace in every file that imports it:
```
use App\Models\Tenant\Tenant;
```
→
```
use App\Models\Tenant\Company;
```

**Files to update (imports + usages):**
- `src/app/Models/Customer.php` — import + `belongsTo(Tenant::class)` → `belongsTo(Company::class)`, relationship method stays `tenant()` → rename to `company()`
- `src/app/Models/Setting.php` — import + relationship
- `src/app/Models/Tenant/GeneralInfo.php` — import + `belongsTo(Tenant::class)` → `belongsTo(Company::class)`, method `tenant()` → `company()`
- `src/app/Models/Tenant/LegalInfo.php` — same as GeneralInfo
- `src/app/Http/Middleware/IdentifyTenant.php` — import + any class-level type hints
- `src/app/Http/Controllers/Tenant/TenantController.php` — import, type hints, variable names (`$tenant` → `$company`)
- `src/app/Http/Controllers/API/V2/TenantController.php` — same
- `src/app/Policies/TenantPolicy.php` — import + type hints
- `src/app/Http/Requests/TenantRequest.php` — import
- `src/app/Http/Resources/V2/TenantResource.php` — import + `new Tenant` → `new Company`
- `src/app/Services/TenantService.php` — import + all usages
- `src/app/Providers/AppServiceProvider.php` — import if present
- `src/app/Providers/AuthServiceProvider.php` — import if present
- `src/database/factories/Tenant/TenantFactory.php` — import + `class TenantFactory` → `class CompanyFactory`, `model = Tenant::class` → `Company::class`
- `src/database/seeders/Tenant/TenantSeeder.php` — import + usages
- `src/database/seeders/Balt/BaltTenantSeeder.php` — import + usages
- `src/routes/breadcrumbs.php` — variable names `$tenant` → `$company`

## 3. Controller file renames
- `src/app/Http/Controllers/Tenant/TenantController.php` → `CompanyController.php`
- `src/app/Http/Controllers/API/V2/TenantController.php` → `CompanyController.php`

Update all `Route::resource` / `Route::get` calls in route files that reference `TenantController` → `CompanyController`.

## 4. Other class renames
- `src/app/Policies/TenantPolicy.php` → `CompanyPolicy.php` (class name inside: `TenantPolicy` → `CompanyPolicy`)
- `src/app/Http/Requests/TenantRequest.php` → `CompanyRequest.php` (class name inside: `TenantRequest` → `CompanyRequest`)
- `src/app/Http/Resources/V2/TenantResource.php` → `CompanyResource.php` (class name: `TenantResource` → `CompanyResource`)
- `src/app/Services/TenantService.php` → `CompanyService.php`
- `src/database/factories/Tenant/TenantFactory.php` → `CompanyFactory.php`
- `src/database/seeders/Tenant/TenantSeeder.php` → `CompanySeeder.php`

## 5. Middleware
- `src/app/Http/Middleware/IdentifyTenant.php` → `IdentifyCompany.php` (class name: `IdentifyTenant` → `IdentifyCompany`)
- Update `src/app/Http/Kernel.php` — middleware alias entry for `IdentifyTenant` → `IdentifyCompany`
- Update all route files that reference `IdentifyTenant` middleware:
  - `src/routes/web.php`
  - `src/routes/api_V1.php`
  - `src/routes/api_V2.php`

## 6. Route parameter
The route parameter `{tenant}` appears in URLs and route files. Rename to `{company}` throughout:
- `src/routes/web.php` — `->prefix('{tenant}')` → `->prefix('{company}')`, middleware group, route model binding
- `src/routes/api_V1.php` — same
- `src/routes/api_V2.php` — same
- `src/routes/breadcrumbs.php` — all `$tenant` parameter names → `$company`
- All tenant-specific route files under `src/routes/tenant/` — update any `{tenant}` references

**Note:** Changing the URL parameter from `{tenant}` to `{company}` is a **breaking change for API consumers**. Confirm this is acceptable before making the change.

## 7. Route model binding
In `src/app/Providers/RouteServiceProvider.php`, if there is explicit route model binding for `Tenant`, update to `Company`.

## 8. Language files
- Rename `src/resources/lang/de/tenants.php` → `companies.php` (update all `__('tenants.*')` calls across views and controllers to `__('companies.*')`)
- Rename `src/resources/lang/en/tenants.php` → `companies.php`

## 9. Views
- Rename directory `src/resources/views/default/tenants/` → `companies/` if it exists
- Update all `@include`, `view()`, and `Route::` calls that reference `tenants.` view paths

## 10. Traits
- `src/app/Traits/TracksTenant.php` — update internal `Tenant::class` reference → `Company::class`

## 11. Config
- `src/config/settings.php` — update any `Tenant` class references

## 12. Tests
Search all test files under `src/tests/` for:
- `Tenant::class`, `Tenant::factory()`, `new Tenant`, `$tenant` variable — update to `Company`
- `TenantController`, `TenantPolicy`, `TenantRequest`, `TenantResource` — update to renamed class names

---

## Verification
After all changes:
1. `grep -r "App\\\\Models\\\\Tenant\\\\Tenant" src/ --include="*.php"` — should return zero results
2. `grep -r "IdentifyTenant" src/ --include="*.php"` — should return zero results (except `IdentifyCompany.php` itself)
3. Run `php artisan route:list` — should produce no errors
4. Run the full test suite
