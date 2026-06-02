<?php

namespace Tests\Feature\TenantIsolation;

use App\Models\Setting;

class SettingIsolationTest extends IsolationTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('destructiveWebEndpoints')]
    public function testRejectsCrossTenant(string $method, string $uriTemplate): void
    {
        $setting = Setting::factory()->for($this->tenantA)->create();

        $this->asOutsider()->assertEndpointRejectsCrossTenant(
            $method,
            fn () => str_replace(
                ['{tenant}', '{setting}'],
                [$this->tenantA->id, $setting->id],
                '/'.$uriTemplate
            )
        );
    }

    public static function destructiveWebEndpoints(): array
    {
        return [
            ['POST', 'tenants/{tenant}/settings'],
            ['PATCH', 'tenants/{tenant}/settings/{setting}'],
            ['DELETE', 'tenants/{tenant}/settings/{setting}'],
            ['POST', 'tenants/{tenant}/settings/test-email-settings'],
        ];
    }
}
