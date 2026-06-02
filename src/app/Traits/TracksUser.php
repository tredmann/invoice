<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait TracksUser
{
    protected static function bootTracksUser(): void
    {
        static::creating(static function ($model): void {
            $model->user_id ??= Auth::id();
        });
    }
}
