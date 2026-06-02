<?php

namespace Tests\Feature\Services\SettingService;

use App\Models\Setting;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\SettingService\SettingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->currentGeneralInfo()->associate(GeneralInfo::factory()->create());
        $this->tenant->currentLegalInfo()->associate(LegalInfo::factory()->create());
        $this->tenant->users()->attach($this->user);
        $this->tenant->save();
    }

    public function testOverwriteSettings()
    {
        $key = 'database.connections.mysql.database';
        $expectedValue = 'invoice_testing_1';

        $this->setting = Setting::factory([
            'key' => $key,
            'value' => Crypt::encryptString($expectedValue),
            'type' => 'string',
        ])
            ->for($this->tenant)
            ->create();

        self::assertNotEquals(config($key), 'invoice_testing_1');

        SettingService::overwriteSettings($this->tenant);

        self::assertEquals(config($key), $expectedValue);
    }

    public function testGetValueAttribute()
    {
        $value = 'test';

        $setting = Setting::make(['value' => Crypt::encryptString($value), 'type' => 'string']);

        self::assertEquals($setting->value, $value);
    }
}
