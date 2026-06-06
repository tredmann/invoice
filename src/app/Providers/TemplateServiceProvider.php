<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Modules\InvoiceTemplates\Models\Templates\BladeInvoiceTemplate;
use App\Modules\InvoiceTemplates\Models\Templates\BladeTemplate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(TemplateManager::class, function (Application $app): \App\Modules\InvoiceTemplates\Models\TemplateManager {
            $templateManager = new TemplateManager();

            // Normal Invoice PDF
            $templateManager->register(
                templateKey: 'invoice.pdf',
                template: new BladeInvoiceTemplate(
                    tenant: TemplateManager::DEFAULT_TENANT,
                    view: 'default.invoices.invoice-pdf'
                )
            );

            // Cancellation Invoice PDF
            $templateManager->register(
                templateKey: 'invoice-cancellation.pdf',
                template: new BladeInvoiceTemplate(
                    tenant: TemplateManager::DEFAULT_TENANT,
                    view: 'default.invoices.cancelled-invoice-pdf'
                )
            );

            // Emails
            $templateManager->register(
                templateKey: 'invoice.email',
                template: new BladeTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default.emails.invoice-mail')
            );

            $templateManager->register(
                templateKey: 'invoice-cancellation.email',
                template: new BladeTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default.emails.invoice-cancellation-mail')
            );

            return $templateManager;
        });
    }

    /** @return void */
    public function boot()
    {
    }
}
