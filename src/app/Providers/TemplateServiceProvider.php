<?php

namespace App\Providers;

use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Modules\InvoiceTemplates\Models\Templates\BladeInvoiceTemplate;
use App\Modules\InvoiceTemplates\Models\Templates\BladeTemplate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    /** @return void */
    public function register()
    {
        $this->app->singleton(TemplateManager::class, function (Application $app) {
            $templateManager = new TemplateManager();

            // Normal Invoice PDF
            $templateManager->register(
                templateKey: 'invoice.pdf',
                template: new BladeInvoiceTemplate(
                    tenant: TemplateManager::DEFAULT_TENANT,
                    view: 'default.invoices.invoice-pdf'
                )
            );

            $templateManager->register(
                templateKey: 'invoice.pdf',
                template: new BladeInvoiceTemplate(
                    tenant: 'goerzwerk',
                    view: 'goerzwerk.pdf.invoice'
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

            $templateManager->register(
                templateKey: 'invoice-cancellation.pdf',
                template: new BladeInvoiceTemplate(
                    tenant: 'goerzwerk',
                    view: 'goerzwerk.pdf.invoice-cancellation'
                )
            );

            // Emails
            $templateManager->register(
                templateKey: 'invoice.email',
                template: new BladeTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default.emails.invoice-mail')
            );

            $templateManager->register(
                templateKey: 'invoice.email',
                template: new BladeTemplate(tenant: 'goerzwerk', view: 'goerzwerk.mail.invoice')
            );

            $templateManager->register(
                templateKey: 'invoice-cancellation.email',
                template: new BladeTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default.emails.invoice-cancellation-mail')
            );

            $templateManager->register(
                templateKey: 'invoice-cancellation.email',
                template: new BladeTemplate(tenant: 'goerzwerk', view: 'goerzwerk.mail.invoice-cancellation')
            );


            return $templateManager;
        });
    }

    /** @return void */
    public function boot()
    {
    }
}
