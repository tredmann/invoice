<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UniqueNumber
 *
 * @property string $id
 * @property string $prefix
 * @property int $current
 * @property int $digits
 * @property string $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UniqueNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UniqueNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UniqueNumber query()
 *
 * @mixin \Eloquent
 */
class UniqueNumber extends Model
{
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['prefix', 'current', 'digits', 'year'];

    protected $attributes = [
        'current' => self::DEFAULT_VALUE,
    ];

    public const DEFAULT_DIGITS = 5;

    public const DEFAULT_VALUE = 0;

    public static function predictedNumber(object $model, int $format = 2): ?string
    {
        $prefix = self::generatePrefixByModel($model);

        $year = date('Y');

        $number = self::where('prefix', $prefix)->where('year', $year)->first();

        $currentNumber = (string) (($number->current ?? config('unique-numbers.'.$model::class.'.value', self::DEFAULT_VALUE)) + 1);

        $zeroFilledNumber = str_pad($currentNumber, self::DEFAULT_DIGITS, '0', STR_PAD_LEFT);

        switch ($format) {
            case 1:
                return $zeroFilledNumber;
            case 2:
                return $year . '-' . $zeroFilledNumber;
            case 3:
                return $prefix . $year . '-' . $zeroFilledNumber;
            case 4:
                return $zeroFilledNumber. '-'.$year;
        }

        return null;
    }

    public static function generatePrefixByModel(object $model): string
    {
        return strtoupper(substr(class_basename($model), 0, 3));
    }

    public static function generateNumber(object $model, int $format = 2): ?string
    {
        $prefix = self::generatePrefixByModel($model);

        $year = date('Y');

        $number = self::where('prefix', $prefix)->where('year', $year)->first();

        if (empty($number)) {
            $defaultValue = config('unique-numbers.'.$model::class.'.value', self::DEFAULT_VALUE);

            $number = new self([
                'prefix' => $prefix,
                'year' => $year,
                'digits' => self::DEFAULT_DIGITS,
                'current' => $defaultValue,
            ]);
            $number->save();
        }

        $number->increment('current');

        $currentNumber = (string) $number->current;
        $zeroFilledNumber = str_pad($currentNumber, self::DEFAULT_DIGITS, '0', STR_PAD_LEFT);

        switch ($format) {
            case 1:
                return $zeroFilledNumber;
            case 2:
                return $year . '-' . $zeroFilledNumber;
            case 3:
                return $prefix . $year . '-' . $zeroFilledNumber;
            case 4:
                return $zeroFilledNumber. '-'.$year;
        }

        return null;
    }

    public static function initNumber(
        object $model,
        int $lengthNumber = self::DEFAULT_DIGITS,
        int $startValue = self::DEFAULT_VALUE,
        ?int $year = null,
    ): self {
        if ($year === null) {
            $year = date('Y');
        }

        $prefix = self::generatePrefixByModel($model);

        $number = new self([
            'prefix' => $prefix,
            'year' => $year,
            'digits' => self::DEFAULT_DIGITS,
            'current' => self::DEFAULT_VALUE
        ]);
        $number->save();

        return $number;
    }
}
