<?php

declare(strict_types=1);

namespace App\Modules\InvoiceTemplates\Models\Templates;

interface Template
{
    /** The tenant key used in the system. There is a default tenant 'default' */
    public function getTenant(): string;

    /** The blade template key to use in view() */
    public function getView(): string;

    /** Function to directly render the view for tenant */
    public function render(array $data): string;
}
