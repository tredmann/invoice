<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\Customer;

class CustomerIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('apiV1Endpoints')]
    public function testV1RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $customer = $this->tenantA->customers->first();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{customer}'],
                [$this->tenantA->id, $customer->id],
                '/api/v1/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('apiV2Endpoints')]
    public function testV2RejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $customer = $this->tenantA->customers->first();

        $this->asOutsiderApi()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{customer}'],
                [$this->tenantA->id, $customer->id],
                '/api/v2/'.$uriTemplate
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testWebRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $customer = $this->tenantA->customers->first();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{customer}'],
                [$this->tenantA->id, $customer->id],
                '/'.$uriTemplate
            )
        );
    }

    /**
     * The V1 store endpoint takes tenant from the URL prefix; tenant_id in the
     * payload must never override that. Asserts the created customer is bound
     * to the URL tenant even when an attacker injects a foreign tenant_id.
     */
    public function testV1IgnoresForeignTenantIdInStorePayload(): void
    {
        $this->asOutsiderApi();

        $before = Customer::where('tenant_id', $this->tenantA->id)->count();

        $response = $this->postJson('/api/v1/'.$this->tenantB->id.'/customers', [
            'tenant_id' => $this->tenantA->id,
            'company' => 'Cross-tenant attempt',
            'name' => 'Mallory',
            'street' => 'X',
            'postal' => '00000',
            'city' => 'X',
        ]);

        self::assertSame(
            $before,
            Customer::where('tenant_id', $this->tenantA->id)->count(),
            'Foreign tenant_id in payload must not land a customer under tenant A.'
        );
        self::assertNotContains($response->getStatusCode(), [500], 'Endpoint must not error.');
    }

    public static function apiV1Endpoints(): array
    {
        return [
            ['GET', '{tenant}/customers'],
            ['POST', '{tenant}/customers'],
            ['GET', '{tenant}/customers/{customer}'],
            ['DELETE', '{tenant}/customers/{customer}'],
            ['PATCH', '{tenant}/customers/{customer}'],
        ];
    }

    public static function apiV2Endpoints(): array
    {
        return [
            ['GET', 'tenant/{tenant}/customers/{customer}'],
            ['GET', 'tenant/{tenant}/customers'],
            ['POST', 'tenant/{tenant}/customers'],
        ];
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', '{tenant}/customers'],
            ['DELETE', '{tenant}/customers/{customer}'],
            ['PATCH', '{tenant}/customers/{customer}'],
        ];
    }
}
