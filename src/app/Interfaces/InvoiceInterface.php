<?php

declare(strict_types=1);

namespace App\Interfaces;

interface InvoiceInterface
{
    public function getInvoiceNumber(): string;

    public function getInvoiceDate(): \DateTimeImmutable;
}
