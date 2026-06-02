<?php

namespace Tests\Concerns;

use App\Enums\Settings as SettingsEnum;
use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Setting;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

trait MakesTenants
{
    protected function makeTenantWithEverything(array $overrides = []): Tenant
    {
        $owner = $overrides['owner'] ?? User::factory()->create();

        $tenant = Tenant::factory(['owner_id' => $owner->id])->create();
        $tenant->users()->attach($owner);

        $generalInfo = GeneralInfo::factory()->create();
        $legalInfo = LegalInfo::factory()->create();

        $tenant->currentGeneralInfo()->associate($generalInfo)->save();
        $tenant->currentLegalInfo()->associate($legalInfo)->save();

        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        CustomerMailReceiver::factory(['customer_id' => $customer->id])->create();

        Setting::create([
            'tenant_id' => $tenant->id,
            'key'       => SettingsEnum::EMAIL_SENDER_ADDRESS->value,
            'value'     => Crypt::encryptString('sender@example.test'),
            'type'      => 'string',
        ]);

        foreach ([
            'mail_mailers_smtp_host'       => 'smtp.example.test',
            'mail_mailers_smtp_port'       => '587',
            'mail_mailers_smtp_username'   => 'user',
            'mail_mailers_smtp_password'   => 'pass',
            'mail_mailers_smtp_encryption' => 'tls',
        ] as $key => $value) {
            Setting::create([
                'tenant_id' => $tenant->id,
                'key'       => $key,
                'value'     => Crypt::encryptString($value),
                'type'      => 'string',
            ]);
        }

        return $tenant->fresh([
            'owner',
            'users',
            'customers.customerMailReceivers',
            'currentGeneralInfo',
            'currentLegalInfo',
            'settings',
        ]);
    }
}
