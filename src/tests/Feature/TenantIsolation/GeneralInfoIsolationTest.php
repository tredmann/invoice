<?php

namespace Tests\Feature\TenantIsolation;

class GeneralInfoIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $generalInfo = $this->tenantA->currentGeneralInfo;

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{generalInfo}'],
                [$this->tenantA->id, $generalInfo->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', 'tenants/{tenant}/generalInfos'],
            ['PATCH', 'tenants/{tenant}/generalInfos/{generalInfo}'],
            ['DELETE', 'tenants/{tenant}/generalInfos/{generalInfo}'],
        ];
    }
}
