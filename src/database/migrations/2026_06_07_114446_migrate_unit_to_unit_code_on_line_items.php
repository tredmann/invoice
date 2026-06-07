<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Map free-text unit strings to UN/CEFACT unit codes.
     * Anything unrecognised falls back to C62 (Piece).
     */
    public function up(): void
    {
        foreach (['line_items', 'master_line_items'] as $table) {
            DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table): void {
                foreach ($rows as $row) {
                    $code = $this->mapUnit($row->unit);
                    DB::table($table)->where('id', $row->id)->update(['unit' => $code]);
                }
            });
        }
    }

    public function down(): void
    {
        // The original free-text values cannot be recovered; this migration is not reversible.
    }

    private function mapUnit(?string $unit): string
    {
        if ($unit === null || trim($unit) === '') {
            return 'C62';
        }

        $lower = strtolower(trim($unit));

        if ($this->matches($lower, ['stück', 'stuck', 'stk', 'stueck', 'piece', 'pcs', 'stk.'])) {
            return 'C62';
        }

        if ($this->matches($lower, ['stunde', 'std', 'hour', 'hr'])) {
            return 'HUR';
        }

        if ($this->matches($lower, ['tag', 'day'])) {
            return 'DAY';
        }

        if ($this->matches($lower, ['monat', 'month', 'mon'])) {
            return 'MON';
        }

        if ($this->matches($lower, ['kilometer', 'kilometre', 'km', 'kmt'])) {
            return 'KMT';
        }

        if ($this->matches($lower, ['meter', 'metre', 'mtr'])) {
            return 'MTR';
        }

        if ($this->matches($lower, ['kilogramm', 'kilogram', 'kg', 'kgm'])) {
            return 'KGM';
        }

        if ($this->matches($lower, ['liter', 'litre', 'ltr'])) {
            return 'LTR';
        }

        if ($this->matches($lower, ['pauschale', 'lumpsum', 'lump sum', 'pauschal', 'ls'])) {
            return 'LS';
        }

        if ($this->matches($lower, ['set'])) {
            return 'SET';
        }

        return 'C62';
    }

    /**
     * @param  list<string>  $keywords
     */
    private function matches(string $lower, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if ($lower === $keyword || str_contains($lower, $keyword)) {
                return true;
            }
        }

        return false;
    }
};
