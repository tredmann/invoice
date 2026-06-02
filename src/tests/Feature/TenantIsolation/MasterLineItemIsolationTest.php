<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\MasterInvoice;
use App\Models\MasterLineItem;

class MasterLineItemIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('apiV1Endpoints')]
    public function testV1RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        [$masterInvoice, $masterLineItem] = $this->makeTenantAMasterInvoiceAndItem();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{masterInvoice}', '{masterLineItem}'],
                [$this->tenantA->id, $masterInvoice->id, $masterLineItem->id],
                '/api/v1/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        [$masterInvoice, $masterLineItem] = $this->makeTenantAMasterInvoiceAndItem();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{masterInvoice}', '{masterLineItem}'],
                [$this->tenantA->id, $masterInvoice->id, $masterLineItem->id],
                '/'.$uriTemplate
            )
        );
    }

    /** @return array{0: MasterInvoice, 1: MasterLineItem} */
    private function makeTenantAMasterInvoiceAndItem(): array
    {
        $masterInvoice = MasterInvoice::factory()->for($this->tenantA->customers->first())->active()->create();
        $masterLineItem = MasterLineItem::factory()->for($masterInvoice)->create();

        return [$masterInvoice, $masterLineItem];
    }

    public static function apiV1Endpoints(): array
    {
        return [
            ['POST', '{tenant}/masterLineItems'],
            ['PATCH', '{tenant}/masterLineItems/{masterLineItem}'],
            ['DELETE', '{tenant}/masterLineItems/{masterLineItem}'],
        ];
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', '{tenant}/masterLineItems'],
            ['PATCH', '{tenant}/masterLineItems/{masterLineItem}'],
            ['DELETE', '{tenant}/masterLineItems/{masterLineItem}'],
        ];
    }
}
