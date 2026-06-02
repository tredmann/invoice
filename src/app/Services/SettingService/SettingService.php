<?php

namespace App\Services\SettingService;

use App\Models\Tenant\Tenant;

class SettingService
{
    public static function overwriteSettings(Tenant $tenant)
    {
        foreach ($tenant->settings as $setting) {
            config([$setting->key => $setting->value]);
        }
    }
}
