# Rename: CustomerMailReceiver → MailRecipient, GeneralInfo → CompanyProfile, LegalInfo → CompanyLegalInfo

## Goal
Rename three supporting models to match the ubiquitous language in `CONTEXT.md`:
- `CustomerMailReceiver` → `MailRecipient` (an email address on a Customer used to deliver invoices)
- `GeneralInfo` → `CompanyProfile` (a Company's contact and address information)
- `LegalInfo` → `CompanyLegalInfo` (a Company's legal registration and banking details)

## Do first
Complete doc 01 (Tenant → Company) first, since `CompanyProfile` and `CompanyLegalInfo` live inside the `App\Models\Tenant\` namespace and that work establishes the `Company` model.

## Do NOT touch in this pass
- Database table names (`customer_mail_receivers`, `general_infos`, `legal_infos`) — no DB schema changes
- Foreign key column names (`legal_info_id`, `general_info_id` on tenants table)

---

## Part A: CustomerMailReceiver → MailRecipient

### A1. Model
**File:** `src/app/Models/CustomerMailReceiver.php`
- Rename file to `MailRecipient.php`
- Rename class: `class CustomerMailReceiver` → `class MailRecipient`
- Add `protected $table = 'customer_mail_receivers';`
- Relationship method `customer()` — keep as-is
- Relationship method `user()` — keep as-is

### A2. Update imports and usages
Replace every `use App\Models\CustomerMailReceiver;` → `use App\Models\MailRecipient;`

**Files to update:**
- `src/app/Models/Customer.php` — import + `customerMailReceivers()` → `mailRecipients()`, `hasMany(CustomerMailReceiver::class)` → `hasMany(MailRecipient::class)`
- `src/app/Http/Controllers/CustomerMailReceiverController.php`
- `src/app/Http/Requests/CustomerMailReceiverRequest.php`
- `src/app/Mail/InvoiceMail.php`
- `src/app/Mail/CancellationInvoiceMail.php`
- `src/app/Services/Invoices/InvoiceMailAttachmentService.php` (check for usages)
- `src/database/factories/CustomerMailReceiverFactory.php`
- `src/database/seeders/CustomerMailReceiverSeeder.php`
- All test files under `src/tests/` referencing `CustomerMailReceiver`

### A3. File renames
- `src/app/Http/Controllers/CustomerMailReceiverController.php` → `MailRecipientController.php` (class: `CustomerMailReceiverController` → `MailRecipientController`)
- `src/app/Http/Requests/CustomerMailReceiverRequest.php` → `MailRecipientRequest.php`
- `src/database/factories/CustomerMailReceiverFactory.php` → `MailRecipientFactory.php`
- `src/database/seeders/CustomerMailReceiverSeeder.php` → `MailRecipientSeeder.php`

### A4. Routes
**`src/routes/web.php`**
- Update route group: `customerMailReceivers` → `mailRecipients`
- Update controller reference to `MailRecipientController`
- Update route names (`customerMailReceivers.create` → `mailRecipients.create`, etc.)

### A5. Views
- Rename directory `src/resources/views/default/customerMailReceivers/` → `mail_recipients/`
- Update all `view('default.customerMailReceivers.*')` references

---

## Part B: GeneralInfo → CompanyProfile

### B1. Model
**File:** `src/app/Models/Tenant/GeneralInfo.php`
- Rename file to `CompanyProfile.php`
- Rename class: `class GeneralInfo` → `class CompanyProfile`
- Add `protected $table = 'general_infos';`
- Rename relationship method: `tenant()` → `company()`, update `belongsTo(Tenant::class)` → `belongsTo(Company::class)` (after doc 01 is done)

### B2. Update Tenant/Company model
**File:** `src/app/Models/Tenant/Tenant.php` (renamed to `Company.php` after doc 01)
- Update import: `use App\Models\Tenant\GeneralInfo;` → `use App\Models\Tenant\CompanyProfile;`
- Rename relationship: `currentGeneralInfo()` → `currentCompanyProfile()`
- Update `belongsTo(GeneralInfo::class)` → `belongsTo(CompanyProfile::class)`
- Update `@property GeneralInfo $currentGeneralInfo` → `@property CompanyProfile $currentCompanyProfile`
- Update `Tenant::setupErrors()` — `$this->currentGeneralInfo` → `$this->currentCompanyProfile`
- Update `Tenant::getSetting()` uses if referencing `currentGeneralInfo`

### B3. Update imports and usages
Replace every `use App\Models\Tenant\GeneralInfo;` → `use App\Models\Tenant\CompanyProfile;`

**Files to update:**
- `src/app/Http/Controllers/Tenant/GeneralInfoController.php`
- `src/app/Policies/GeneralInfoPolicy.php`
- `src/app/Http/Requests/GeneralInfoRequest.php`
- `src/database/factories/Tenant/GeneralInfoFactory.php`
- `src/database/seeders/Tenant/GeneralInfoSeeder.php`
- `src/database/seeders/Balt/BaltGeneralInfoSeeder.php`
- All test files referencing `GeneralInfo`

### B4. File renames
- `src/app/Http/Controllers/Tenant/GeneralInfoController.php` → `CompanyProfileController.php`
- `src/app/Policies/GeneralInfoPolicy.php` → `CompanyProfilePolicy.php`
- `src/app/Http/Requests/GeneralInfoRequest.php` → `CompanyProfileRequest.php`
- `src/database/factories/Tenant/GeneralInfoFactory.php` → `CompanyProfileFactory.php`
- `src/database/seeders/Tenant/GeneralInfoSeeder.php` → `CompanyProfileSeeder.php`

### B5. Routes
- `src/routes/tenant/generalInfos.php` → `companyProfiles.php`
- Update all route names (`generalInfos.*` → `companyProfiles.*`) and controller references

### B6. Language files
- `src/resources/lang/de/generalInfos.php` → `companyProfiles.php`
- `src/resources/lang/en/generalInfos.php` → `companyProfiles.php`
- Update all `__('generalInfos.*')` calls

### B7. Views
- Rename directory `src/resources/views/default/generalInfos/` → `company_profiles/`
- Update all `view('default.generalInfos.*')` references

---

## Part C: LegalInfo → CompanyLegalInfo

### C1. Model
**File:** `src/app/Models/Tenant/LegalInfo.php`
- Rename file to `CompanyLegalInfo.php`
- Rename class: `class LegalInfo` → `class CompanyLegalInfo`
- Add `protected $table = 'legal_infos';`
- Rename relationship method: `tenant()` → `company()`, update `belongsTo(Tenant::class)` → `belongsTo(Company::class)`

### C2. Update Tenant/Company model
**File:** `src/app/Models/Tenant/Company.php` (after doc 01)
- Update import: `use App\Models\Tenant\LegalInfo;` → `use App\Models\Tenant\CompanyLegalInfo;`
- Rename relationship: `currentLegalInfo()` → `currentCompanyLegalInfo()`
- Update `belongsTo(LegalInfo::class)` → `belongsTo(CompanyLegalInfo::class)`
- Update `@property LegalInfo $currentLegalInfo` → `@property CompanyLegalInfo $currentCompanyLegalInfo`
- Update `Tenant::setupErrors()` — `$this->currentLegalInfo` → `$this->currentCompanyLegalInfo`

### C3. Update imports and usages
Replace every `use App\Models\Tenant\LegalInfo;` → `use App\Models\Tenant\CompanyLegalInfo;`

**Files to update:**
- `src/app/Http/Controllers/Tenant/LegalInfoController.php`
- `src/app/Policies/LegalInfoPolicy.php`
- `src/app/Http/Requests/LegalInfoRequest.php`
- `src/database/factories/Tenant/LegalInfoFactory.php`
- `src/database/seeders/Tenant/LegalInfoSeeder.php`
- `src/database/seeders/Balt/BaltLegalInfoSeeder.php`
- All Blade invoice templates that access `$invoice->customer->tenant->currentLegalInfo` — update to `currentCompanyLegalInfo`
- All test files referencing `LegalInfo`

### C4. File renames
- `src/app/Http/Controllers/Tenant/LegalInfoController.php` → `CompanyLegalInfoController.php`
- `src/app/Policies/LegalInfoPolicy.php` → `CompanyLegalInfoPolicy.php`
- `src/app/Http/Requests/LegalInfoRequest.php` → `CompanyLegalInfoRequest.php`
- `src/database/factories/Tenant/LegalInfoFactory.php` → `CompanyLegalInfoFactory.php`
- `src/database/seeders/Tenant/LegalInfoSeeder.php` → `CompanyLegalInfoSeeder.php`

### C5. Routes
- `src/routes/tenant/legalInfos.php` → `companyLegalInfos.php`
- Update route names (`legalInfos.*` → `companyLegalInfos.*`) and controller references

### C6. Language files
- `src/resources/lang/de/legalInfos.php` → `companyLegalInfos.php`
- `src/resources/lang/en/legalInfos.php` → `companyLegalInfos.php`
- Update all `__('legalInfos.*')` calls

### C7. Views
- Rename directory `src/resources/views/default/legalInfos/` → `company_legal_infos/`
- Update all `view('default.legalInfos.*')` references
- Update all Blade invoice PDF templates that render legal info fields

---

## Verification
After all changes:
1. `grep -r "CustomerMailReceiver\|GeneralInfo\|LegalInfo" src/ --include="*.php"` — should return zero results
2. `grep -r "currentGeneralInfo\|currentLegalInfo\|customerMailReceivers" src/ --include="*.php"` — should return zero results
3. `grep -r "generalInfos\|legalInfos\|customerMailReceivers" src/resources/ --include="*.blade.php"` — should return zero results
4. Run `php artisan route:list` — no errors
5. Run the full test suite
