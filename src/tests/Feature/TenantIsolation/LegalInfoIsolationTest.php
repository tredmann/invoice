<?php

namespace Tests\Feature\TenantIsolation;

class LegalInfoIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $legalInfo = $this->tenantA->currentLegalInfo;

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{legalInfo}'],
                [$this->tenantA->id, $legalInfo->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', 'tenants/{tenant}/legalInfos'],
            ['PATCH', 'tenants/{tenant}/legalInfos/{legalInfo}'],
            ['DELETE', 'tenants/{tenant}/legalInfos/{legalInfo}'],
        ];
    }
}
