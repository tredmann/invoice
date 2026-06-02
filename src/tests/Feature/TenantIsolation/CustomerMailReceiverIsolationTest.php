<?php

namespace Tests\Feature\TenantIsolation;

class CustomerMailReceiverIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $customer = $this->tenantA->customers->first();
        $receiver = $customer->customerMailReceivers->first();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{customer}', '{customerMailReceiver}'],
                [$this->tenantA->id, $customer->id, $receiver->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', '{tenant}/customerMailReceivers'],
            ['PATCH', '{tenant}/customerMailReceivers/{customerMailReceiver}'],
            ['DELETE', '{tenant}/customerMailReceivers/{customerMailReceiver}'],
        ];
    }
}
