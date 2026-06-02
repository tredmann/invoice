<?php

namespace App\Models;

use App\Services\Invoices\InvoiceService;
use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineItem
 *
 * @property string $id
 * @property User $user
 * @property string $invoice_id
 * @property Invoice $invoice
 * @property int $user_id
 * @property int $quantity
 * @property int $price_each
 * @property string $currency
 * @property int $without_tax
 * @property float $tax_rate
 * @property int $with_tax
 * @property string $unit
 * @property string $detail
 * @property string $detail_plus
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LineItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LineItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LineItem query()
 *
 * @mixin \Eloquent
 */
class LineItem extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    protected static function boot(): void
    {
        parent::boot();

        static::created(static function ($model) {
            InvoiceService::totalsUpdate($model->invoice);
            InvoiceService::setCurrencyIfNull($model->invoice, $model);
        });

        static::updated(static function ($model) {
            InvoiceService::totalsUpdate($model->invoice);
        });

        static::deleted(static function ($model) {
            InvoiceService::totalsUpdate($model->invoice);
        });
    }

    protected $keyType = 'string';

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'invoice_id',
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
}
