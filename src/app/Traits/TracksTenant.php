<?php

namespace App\Traits;

use App\Models\Tenant\Tenant;

trait TracksTenant
{
    protected static function bootTracksTenant(): void
    {
        static::creating(static function ($model): void {
            $model->tenant_id ??= app(Tenant::class)->id;
        });
    }
}
