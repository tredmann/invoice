<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\UnitCode;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<UnitCode, UnitCode|string>
 */
class UnitCodeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): UnitCode
    {
        return UnitCode::tryFrom((string) $value) ?? UnitCode::Piece;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof UnitCode) {
            return $value->value;
        }

        return (string) $value;
    }
}
