<?php

namespace Tests\Unit\Modules\TemplateManager;

use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Modules\InvoiceTemplates\Models\Templates\BladeInvoiceTemplate;
use Tests\TestCase;

class TemplateManagerTest extends TestCase
{
    public function testItWillGetTenantTemplate(): void
    {
        $tenantTemplate = new BladeInvoiceTemplate(tenant: 'test', view: 'test');
        $defaultTemplate = new BladeInvoiceTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default');

        $templateManager = new TemplateManager();

        $templateManager->register(templateKey: 'test.pdf', template: $tenantTemplate);
        $templateManager->register(templateKey: 'test.pdf', template: $defaultTemplate);

        self::assertEquals($tenantTemplate, $templateManager->getTemplate(templateKey: 'test.pdf', tenantKey: 'test'));
    }

    /**
     * Test to register two templates for tenants 'test' and 'default' and then try to get template for 'test2', which
     * doesn't exist, so it should return the default template
     */
    public function testItWillReturnDefaultTemplate(): void
    {
        $tenantTemplate = new BladeInvoiceTemplate(tenant: 'test', view: 'test');
        $defaultTemplate = new BladeInvoiceTemplate(tenant: TemplateManager::DEFAULT_TENANT, view: 'default');

        $templateManager = new TemplateManager();

        $templateManager->register(templateKey: 'test.pdf', template: $tenantTemplate);
        $templateManager->register(templateKey: 'test.pdf', template: $defaultTemplate);

        self::assertEquals($defaultTemplate, $templateManager->getTemplate(templateKey: 'test.pdf', tenantKey: 'test2'));
    }
}
