<?php

declare(strict_types=1);

namespace App\Enums;

enum UnitCode: string
{
    case Piece = 'C62';     // Stück
    case Hour = 'HUR';      // Stunde
    case Day = 'DAY';       // Tag
    case Month = 'MON';     // Monat
    case Metre = 'MTR';     // Meter
    case Kilometre = 'KMT'; // Kilometer
    case Kilogram = 'KGM';  // Kilogramm
    case Litre = 'LTR';     // Liter
    case Set = 'SET';       // Set / Pauschal
    case LumpSum = 'LS';    // Pauschale (Lump Sum)

    public function label(): string
    {
        return match ($this) {
            self::Piece => 'Stück',
            self::Hour => 'Stunde',
            self::Day => 'Tag',
            self::Month => 'Monat',
            self::Metre => 'Meter',
            self::Kilometre => 'Kilometer',
            self::Kilogram => 'Kilogramm',
            self::Litre => 'Liter',
            self::Set => 'Set/Pauschal',
            self::LumpSum => 'Pauschale',
        };
    }
}
