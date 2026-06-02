<?php

namespace App\Modules\InvoiceTemplates\Models\Templates;

class BladeTemplate implements Template
{
    /**
     * @param string $tenant The tenant for which the view should be loaded
     * @param string $view The string of the view which can be loaded by blade
     */
    public function __construct(
        private readonly string $tenant,
        private readonly string $view,
    ) {
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function render(array $data): string
    {
        return view($this->view, $data)->render();
    }
}
