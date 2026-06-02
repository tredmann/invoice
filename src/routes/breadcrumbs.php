<?php

// routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Setting;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\UniqueNumber;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Laravel\Sanctum\PersonalAccessToken;

// Tenants
Breadcrumbs::for('tenants.index', function (BreadcrumbTrail $trail) {
    $trail->push('Tenants', route('tenants.index'));
});

// Tenants > Create
Breadcrumbs::for('tenants.create', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.index');
    $trail->push(__('translate.create'), route('tenants.create'));
});

// Tenants > [Tenant->name]
Breadcrumbs::for('tenants.show', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.index');
    $trail->push(app(Tenant::class)->name, app(Tenant::class)->route('tenants.users'));
});

// Tenants > [Tenant->name] > Edit
Breadcrumbs::for('tenants.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('translate.edit'), app(Tenant::class)->route('tenants.edit'));
});

// Tenants > [Tenant->name] > Users
Breadcrumbs::for('tenants.users', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('tenants.users'), app(Tenant::class)->route('tenants.users'));
});

// Tenants > [Tenant->name] > Users > Invite User
Breadcrumbs::for('tenants.invite-user-form', function ($trail) {
    $trail->parent('tenants.users');
    $trail->push(__('tenants.invite_user'), app(Tenant::class)->route('tenants.invite-user-form'));
});

// Tenants > [Tenant->name] > Legal Infos
Breadcrumbs::for('legalInfos.index', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('legalInfos.legal_infos'), app(Tenant::class)->route('legalInfos.index'));
});

// Tenants > [Tenant->name] > Legal Infos > Create
Breadcrumbs::for('legalInfos.create', function (BreadcrumbTrail $trail) {
    $trail->parent('legalInfos.index');
    $trail->push(__('translate.create'), app(Tenant::class)->route('legalInfos.create'));
});

// Tenants > [Tenant->name] > [Legal Info] > Edit
Breadcrumbs::for('legalInfos.edit', function (BreadcrumbTrail $trail, LegalInfo $legalInfo) {
    $trail->parent('legalInfos.index');
    $trail->push(__('translate.edit'), app(Tenant::class)->route('legalInfos.edit', ['legalInfo' => $legalInfo]));
});

// Tenants > [Tenant->name] > General Infos
Breadcrumbs::for('generalInfos.index', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('generalInfos.general_infos'), app(Tenant::class)->route('generalInfos.index'));
});

// Tenants > [Tenant->name] > General Infos > Create
Breadcrumbs::for('generalInfos.create', function (BreadcrumbTrail $trail) {
    $trail->parent('generalInfos.index');
    $trail->push(__('translate.create'), app(Tenant::class)->route('generalInfos.create'));
});

// Tenants > [Tenant->name] > General Infos > Edit
Breadcrumbs::for('generalInfos.edit', function (BreadcrumbTrail $trail, GeneralInfo $generalInfo) {
    $trail->parent('generalInfos.index');
    $trail->push(__('translate.edit'), app(Tenant::class)->route('generalInfos.edit', ['generalInfo' => $generalInfo]));
});

// Tenants > [Tenant->name] > Settings
Breadcrumbs::for('settings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('settings.settings'), app(Tenant::class)->route('settings.index'));
});

// Tenants > [Tenant->name] > Settings > Test Email Settings
Breadcrumbs::for('settings.testEmailSettingsForm', function (BreadcrumbTrail $trail) {
    $trail->parent('tenants.show');
    $trail->push(__('settings.test_email_settings'), app(Tenant::class)->route('settings.testEmailSettings'));
});

// Tenants > [Tenant->name] > Settings > Create
Breadcrumbs::for('settings.create', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.index');
    $trail->push(__('translate.create'), app(Tenant::class)->route('settings.create'));
});

// Tenants > [Tenant->name] > Settings > Edit
Breadcrumbs::for('settings.edit', function (BreadcrumbTrail $trail, Setting $setting) {
    $trail->parent('settings.index');
    $trail->push(__('translate.edit'), app(Tenant::class)->route('settings.edit', ['setting' => $setting]));
});

// Customers
Breadcrumbs::for('customers.index', function (BreadcrumbTrail $trail, Tenant $tenant) {
    $trail->push(__('customers.customers'), app(Tenant::class)->route('customers.index', ['tenant' => $tenant]));
});

// Customers > Create
Breadcrumbs::for('customers.create', function (BreadcrumbTrail $trail, Tenant $tenant) {
    $trail->parent('customers.index');
    $trail->push(__('translate.create'), route('customers.create', ['tenant' => $tenant]));
});

// Customers > [Customer->name/Customer->company]
Breadcrumbs::for('customers.show', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.index');
    $trail->push($customer->company, app(Tenant::class)->route('customers.invoices', ['customer' => $customer]));
});

// Customers > [Customer->name/Customer->company] > Edit
Breadcrumbs::for('customers.edit', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.show', $customer);
    $trail->push(__('translate.edit'), app(Tenant::class)->route('customers.edit', ['customer' => $customer]));
});

// Customers > [Customer->name/Customer->company] > Invoices
Breadcrumbs::for('customers.invoices', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.show', $customer);
    $trail->push(__('customers.invoices'), app(Tenant::class)->route('customers.invoices', ['customer' => $customer]));
});

// Customers > [Customer->name/Customer->company] > Invoices > [Invoice->invoice_no] > Conclude
Breadcrumbs::for('invoices.conclude', function (BreadcrumbTrail $trail, Invoice $invoice) {
    $trail->parent('customers.invoices', $invoice->customer);
    $trail->push(__('invoices.conclude'), app(Tenant::class)->route('invoices.conclude', ['invoice' => $invoice]));
});

// Customers > [Customer->name/Customer->company] > Invoices > [Invoice->invoice_no] > Line Items
Breadcrumbs::for('invoices.show', function (BreadcrumbTrail $trail, Invoice $invoice) {
    $trail->parent('customers.invoices', $invoice->customer);
    $trail->push($invoice->invoice_no ?: '('.UniqueNumber::predictedNumber(model: $invoice, format: config('unique-numbers.'.Invoice::class.'.format', 2)).')', app(Tenant::class)->route('invoices.show', ['invoice' => $invoice]));
});

// Customers > [Customer->name/Customer->company] > Invoices > [Invoice->invoice_no] > Line Items > Create
Breadcrumbs::for('lineItems.create', function (BreadcrumbTrail $trail, Invoice $invoice) {
    $trail->parent('invoices.show', $invoice);
    $trail->push(__('translate.create'), app(Tenant::class)->route('invoices.show', ['invoice' => $invoice]));
});

// Customers > [Customer->name/Customer->company] > Master Invoices
Breadcrumbs::for('customers.masterInvoices', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.show', $customer);
    $trail->push(
        __('customers.master_invoices'),
        app(Tenant::class)->route('customers.masterInvoices', ['customer' => $customer]),
    );
});

// Customers > [Customer->name/Customer->company] > Master Invoices > [Master Invoice] > Activate
Breadcrumbs::for('masterInvoices.activate', function (BreadcrumbTrail $trail, MasterInvoice $masterInvoice) {
    $trail->parent('customers.masterInvoices', $masterInvoice->customer);
    $trail->push(__('masterInvoices.activate'), app(Tenant::class)->route('masterInvoices.activate', ['masterInvoice' => $masterInvoice]));
});

// Customers > [Customer->name/Customer->company] > Master Invoices > [Master Invoice] > Master Line Items
Breadcrumbs::for('masterInvoices.masterLineItems', function (BreadcrumbTrail $trail, MasterInvoice $masterInvoice) {
    $trail->parent('customers.masterInvoices', $masterInvoice->customer);
    $trail->push(__('masterInvoices.master_invoice'), app(Tenant::class)->route('masterInvoices.masterLineItems', ['masterInvoice' => $masterInvoice]));
    $trail->push(__('masterLineItems.master_line_items'));
});

// Customers > [Customer->name/Customer->company] > Master Invoices > [Master Invoice] > Master Line Items > Create
Breadcrumbs::for('masterLineItems.create', function (BreadcrumbTrail $trail, MasterInvoice $masterInvoice) {
    $trail->parent('masterInvoices.masterLineItems', $masterInvoice);
    $trail->push(__('translate.create'), $masterInvoice->customer->tenant->route('masterLineItems.create', ['masterInvoice' => $masterInvoice]));
});

// Customers > [Customer->name/Customer->company] > Master Invoices > [Master Invoice] > Master Line Items > Edit
Breadcrumbs::for('masterLineItems.edit', function (BreadcrumbTrail $trail, MasterLineItem $masterLineItem) {
    $trail->parent('masterInvoices.masterLineItems', $masterLineItem->masterInvoice);
    $trail->push(__('translate.edit'), app(Tenant::class)->route('masterLineItems.edit', ['masterLineItem' => $masterLineItem]));
});

// Customers > [Customer->name/Customer->company] > Mail Receivers
Breadcrumbs::for('customers.mailReceivers', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.show', $customer);
    $trail->push(
        __('customers.mail_receivers'),
        app(Tenant::class)->route('customers.mailReceivers', ['customer' => $customer]),
    );
});

// Customers > [Customer->name/Customer->company] > Mail Receivers > Create
Breadcrumbs::for('customerMailReceivers.create', function (BreadcrumbTrail $trail, Customer $customer) {
    $trail->parent('customers.mailReceivers', $customer);
    $trail->push(
        __('translate.create'),
        app(Tenant::class)->route('customerMailReceivers.create', ['customer' => $customer]),
    );
});

// Customers > [Customer->name/Customer->company] > Mail Receivers > Edit
Breadcrumbs::for('customerMailReceivers.edit', function (BreadcrumbTrail $trail, CustomerMailReceiver $customerMailReceiver) {
    $trail->parent('customers.mailReceivers', $customerMailReceiver->customer);
    $trail->push(
        __('translate.edit'),
        app(Tenant::class)->route('customerMailReceivers.edit', ['customerMailReceiver' => $customerMailReceiver]),
    );
});

// Admin > User Panel
Breadcrumbs::for('admin.user-panel.index', function (BreadcrumbTrail $trail) {
    $trail->push(__('admin/user-panel.user-panel'), route('admin.user-panel.index'));
});

// Admin > User Panel > Create
Breadcrumbs::for('admin.user-panel.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-panel.index');
    $trail->push(__('translate.create'), route('admin.user-panel.create'));
});

// Admin > User Panel > Edit
Breadcrumbs::for('admin.user-panel.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.user-panel.index');
    $trail->push(__('translate.edit'), route('admin.user-panel.edit', ['user' => $user]));
});

// User / API Tokens
Breadcrumbs::for('api-tokens.index', function (BreadcrumbTrail $trail) {
    $trail->push(__('api-token-manager.api_tokens'), route('api-tokens.index'));
});

// User / API Tokens > Update
Breadcrumbs::for('api-tokens.updateForm', function (BreadcrumbTrail $trail, PersonalAccessToken $personalAccessToken) {
    $trail->parent('api-tokens.index');
    $trail->push(__('api-token-manager.update_api_token'), route('api-tokens.updateForm', ['personalAccessToken' => $personalAccessToken]));
});

Breadcrumbs::for('invoices.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
});
