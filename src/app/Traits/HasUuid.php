<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(static function ($model): void {
            $model->{$model->getKeyName()} = Str::uuid();
        });
    }
}
