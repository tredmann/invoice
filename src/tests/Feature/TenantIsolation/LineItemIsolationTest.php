<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\Invoice;
use App\Models\LineItem;

class LineItemIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('apiV1Endpoints')]
    public function testV1RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        [$invoice, $lineItem] = $this->makeTenantAInvoiceAndLineItem();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{invoice}', '{lineItem}'],
                [$this->tenantA->id, $invoice->id, $lineItem->id],
                '/api/v1/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        [$invoice, $lineItem] = $this->makeTenantAInvoiceAndLineItem();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{invoice}', '{lineItem}'],
                [$this->tenantA->id, $invoice->id, $lineItem->id],
                '/'.$uriTemplate
            )
        );
    }

    /** @return array{0: Invoice, 1: LineItem} */
    private function makeTenantAInvoiceAndLineItem(): array
    {
        $invoice = Invoice::factory()->for($this->tenantA->customers->first())->create();
        $lineItem = LineItem::factory()->for($invoice)->create();

        return [$invoice, $lineItem];
    }

    public static function apiV1Endpoints(): array
    {
        return [
            ['POST', '{tenant}/lineItems/{invoice}'],
            ['PATCH', '{tenant}/lineItems/{lineItem}'],
            ['DELETE', '{tenant}/lineItems/{lineItem}'],
        ];
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', '{tenant}/lineItems/{invoice}'],
            ['PATCH', '{tenant}/lineItems/{lineItem}'],
            ['DELETE', '{tenant}/lineItems/{lineItem}'],
        ];
    }
}
