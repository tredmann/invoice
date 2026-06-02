<?php

declare(strict_types=1);

namespace App\Services\SettingService;

use App\Models\Tenant\Tenant;

class SettingService
{
    public static function overwriteSettings(Tenant $tenant): void
    {
        foreach ($tenant->settings as $setting) {
            config([$setting->key => $setting->value]);
        }
    }
}
