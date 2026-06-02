<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\Invoice;

class InvoiceIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('apiV1Endpoints')]
    public function testV1RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $invoice = Invoice::factory()->for($this->tenantA->customers->first())->create();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{invoice}'],
                [$this->tenantA->id, $invoice->id],
                '/api/v1/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('apiV2Endpoints')]
    public function testV2RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $invoice = Invoice::factory()->for($this->tenantA->customers->first())->create();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{invoice}'],
                [$this->tenantA->id, $invoice->id],
                '/api/v2/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $invoice = Invoice::factory()->for($this->tenantA->customers->first())->open()->create();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{invoice}'],
                [$this->tenantA->id, $invoice->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function apiV1Endpoints(): array
    {
        return [
            ['GET', '{tenant}/invoices'],
            ['POST', '{tenant}/invoices'],
            ['GET', '{tenant}/invoices/{invoice}'],
            ['DELETE', '{tenant}/invoices/{invoice}'],
            ['PATCH', '{tenant}/invoices/{invoice}/open'],
            ['PATCH', '{tenant}/invoices/{invoice}/paid'],
        ];
    }

    public static function apiV2Endpoints(): array
    {
        return [
            ['GET', 'tenant/{tenant}'],
            ['GET', 'tenant/{tenant}/invoices/{invoice}'],
            ['GET', 'tenant/{tenant}/invoices'],
            ['POST', 'tenant/{tenant}/invoices'],
            ['GET', 'invoices/{invoice}'],
        ];
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['DELETE', '{tenant}/invoices/{invoice}'],
            ['PATCH', '{tenant}/invoices/{invoice}/open'],
            ['PATCH', '{tenant}/invoices/{invoice}/paid'],
            ['PATCH', '{tenant}/invoices/{invoice}/cancel'],
            ['POST', '{tenant}/invoices/{invoice}/sendmail'],
        ];
    }
}
