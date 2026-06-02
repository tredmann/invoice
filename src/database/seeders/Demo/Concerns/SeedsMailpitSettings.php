<?php

namespace Database\Seeders\Demo\Concerns;

use App\Enums\Settings as SettingsEnum;
use App\Models\Setting;
use App\Models\Tenant\Tenant;
use Illuminate\Support\Facades\Crypt;

trait SeedsMailpitSettings
{
    /**
     * Point this tenant's mailer at the local Mailpit container.
     *
     * Only overrides what differs from the global mail config (`.env` already
     * targets `invoice-mailpit:1025` with no auth / no encryption), plus the
     * per-tenant sender address so demo invoices show a recognisable From.
     */
    private function seedMailpitSettings(Tenant $tenant, string $senderAddress): void
    {
        $settings = [
            ['mail.mailers.smtp.host', 'invoice-mailpit', 'string'],
            ['mail.mailers.smtp.port', '1025', 'integer'],
            [SettingsEnum::EMAIL_SENDER_ADDRESS->value, $senderAddress, 'string'],
        ];

        foreach ($settings as [$key, $value, $type]) {
            Setting::create([
                'tenant_id' => $tenant->id,
                'key' => $key,
                'value' => Crypt::encryptString($value),
                'type' => $type,
            ]);
        }
    }
}
