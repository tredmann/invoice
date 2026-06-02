<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\MasterInvoice;

class MasterInvoiceIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('apiV1Endpoints')]
    public function testV1RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $master = MasterInvoice::factory()->for($this->tenantA->customers->first())->create();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{masterInvoice}'],
                [$this->tenantA->id, $master->id],
                '/api/v1/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $master = MasterInvoice::factory()->for($this->tenantA->customers->first())->active()->create();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{masterInvoice}'],
                [$this->tenantA->id, $master->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function apiV1Endpoints(): array
    {
        return [
            ['POST', '{tenant}/masterInvoices'],
            ['GET', '{tenant}/masterInvoices/{masterInvoice}'],
            ['DELETE', '{tenant}/masterInvoices/{masterInvoice}'],
            ['PATCH', '{tenant}/masterInvoices/{masterInvoice}/active'],
            ['PATCH', '{tenant}/masterInvoices/{masterInvoice}/pause'],
        ];
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['DELETE', '{tenant}/masterInvoices/{masterInvoice}'],
            ['PATCH', '{tenant}/masterInvoices/{masterInvoice}/active'],
            ['PATCH', '{tenant}/masterInvoices/{masterInvoice}/pause'],
        ];
    }
}
