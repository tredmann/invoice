<?php

namespace App\Traits;

use App\Models\UniqueNumber;

trait HasUniqueNumber
{
    protected static function bootHasUniqueNumber(): void
    {
        static::creating(static function ($model) {
            $model->{class_basename($model) . '_no'} = UniqueNumber::generateNumber(
                model: $model,
                format: config('unique-numbers.'.$model::class.'.format', 2)
            );
        });
    }
}
