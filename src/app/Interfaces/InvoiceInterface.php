<?php

namespace App\Interfaces;

interface InvoiceInterface
{
    public function getInvoiceNumber(): string;

    public function getInvoiceDate(): \DateTimeImmutable;
}
