<?php

namespace App\Modules\InvoiceTemplates\Models;

use App\Modules\InvoiceTemplates\Models\Templates\Template;

/**
 * The template manager holds templates for different tenants. You can request them, by providing a template key.
 */
class TemplateManager
{
    /** @var string The default tenant key for a template */
    public const DEFAULT_TENANT = 'default';

    private array $templates;

    public function __construct()
    {
        $this->templates = [];
    }

    /**
     * You can add a template with a key to access it later via the template manager
     *
     * @param string $templateKey The key we want to access the template later,
     *                    recommendation would be something like the original blade key
     */
    public function register(string $templateKey, Template $template): self
    {
        $this->templates[$templateKey][$template->getTenant()] = $template;

        return $this;
    }

    /**
     * Tries to return the template identified by templateKey for a tenant, identified by tenantKey
     *
     * If there is no template for the tenant configured, it will try to return the default template.
     *
     * If there is no default template or the template doesn't even exist, then null will be return
     */
    public function getTemplate(string $templateKey, string $tenantKey): ?Template
    {
        $templates = $this->templates[$templateKey] ?? [];

        if (count($templates) === 0) {
            return null;
        }

        $template = $templates[$tenantKey] ?? null;

        if ($template === null) {
            return $templates[self::DEFAULT_TENANT] ?? null;
        }

        return $template;
    }
}
