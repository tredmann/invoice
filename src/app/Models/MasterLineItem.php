<?php

namespace App\Models;

use App\Casts\UnitCodeCast;
use App\Enums\UnitCode;
use App\Services\MasterInvoices\MasterInvoiceService;
use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterLineItem
 *
 * @property string $id
 * @property User $user
 * @property string $master_invoice_id
 * @property MasterInvoice $masterInvoice
 * @property int $user_id
 * @property int $quantity
 * @property int $price_each
 * @property string $currency
 * @property int $without_tax
 * @property float $tax_rate
 * @property int $with_tax
 * @property UnitCode $unit
 * @property string $detail
 * @property string|null $detail_plus
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MasterLineItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterLineItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterLineItem query()
 *
 * @mixin \Eloquent
 */
class MasterLineItem extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    #[\Override]
    protected static function boot(): void
    {
        parent::boot();

        static::created(static function (\App\Models\MasterLineItem $model): void {
            MasterInvoiceService::totalsUpdate($model->masterInvoice);
            MasterInvoiceService::setCurrencyIfNull($model->masterInvoice, $model);
        });

        static::updated(static function ($model): void {
            MasterInvoiceService::totalsUpdate($model->masterInvoice);
        });

        static::deleted(static function ($model): void {
            MasterInvoiceService::totalsUpdate($model->masterInvoice);
        });
    }

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function masterInvoice()
    {
        return $this->belongsTo(MasterInvoice::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'master_invoice_id',
        'user_id',
        'quantity',
        'price_each',
        'currency',
        'without_tax',
        'tax_rate',
        'with_tax',
        'unit',
        'detail',
        'detail_plus',
    ];
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'unit' => UnitCodeCast::class,
        ];
    }
}
