<?php

declare(strict_types=1);

namespace Tests\Unit\Casts;

use App\Casts\UnitCodeCast;
use App\Enums\UnitCode;
use App\Models\LineItem;
use PHPUnit\Framework\TestCase;

class UnitCodeCastTest extends TestCase
{
    public function testGetReturnsUnitCodeForValidValue(): void
    {
        $cast = new UnitCodeCast();
        $result = $cast->get(new LineItem(), 'unit', 'HUR', []);
        self::assertSame(UnitCode::Hour, $result);
    }

    public function testGetFallsBackToPieceForUnknownValue(): void
    {
        $cast = new UnitCodeCast();
        $result = $cast->get(new LineItem(), 'unit', 'invalid_legacy_string', []);
        self::assertSame(UnitCode::Piece, $result);
    }

    public function testGetFallsBackToPieceForNull(): void
    {
        $cast = new UnitCodeCast();
        $result = $cast->get(new LineItem(), 'unit', null, []);
        self::assertSame(UnitCode::Piece, $result);
    }

    public function testSetReturnsBackingValue(): void
    {
        $cast = new UnitCodeCast();
        $result = $cast->set(new LineItem(), 'unit', UnitCode::Hour, []);
        self::assertSame('HUR', $result);
    }

    public function testSetAcceptsStringValue(): void
    {
        $cast = new UnitCodeCast();
        $result = $cast->set(new LineItem(), 'unit', 'HUR', []);
        self::assertSame('HUR', $result);
    }
}
