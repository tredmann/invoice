<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class MasterInvoice
 *
 * @property string $id
 * @property User $user
 * @property Customer $customer
 * @property Collection<MasterLineItem> $masterLineItems
 * @property string $customer_id
 * @property int $user_id
 * @property string $billing_frequency
 * @property string $next_print
 * @property int $days_till_due
 * @property string $status
 * @property string|null $currency
 * @property int $total_with_tax
 * @property int $total_without_tax
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MasterInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterInvoice query()
 *
 * @mixin \Eloquent
 */
class MasterInvoice extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const DAYS_TILL_DUE = [7, 14, 30];

    public const BILLING_FREQUENCIES = [
        self::BILLING_DAY,
        self::BILLING_WEEK,
        self::BILLING_MONTH,
        self::BILLING_QUARTER_YEAR,
        self::BILLING_HALF_YEAR,
    ];

    public const BILLING_DAY = '1 day';

    public const BILLING_WEEK = '1 week';

    public const BILLING_MONTH = '1 month';

    public const BILLING_QUARTER_YEAR = '3 months';

    public const BILLING_HALF_YEAR = '6 months';

    public function buildPerformedWhen(): string
    {
        $this->next_print = Carbon::parse($this->next_print);
        return match ($this->billing_frequency) {
            self::BILLING_DAY => $this->next_print->isoFormat('dddd, MMMM YYYY'),
            self::BILLING_WEEK => 'KW ' . $this->next_print->isoFormat('w YYYY'),
            self::BILLING_MONTH => $this->next_print->isoFormat('MMMM YYYY'),
            self::BILLING_QUARTER_YEAR => $this->next_print->subMonths(3)->isoFormat('MMMM YYYY') . ' - ' . now()->isoFormat('MMMM YYYY'),
            self::BILLING_HALF_YEAR => $this->next_print->subMonths(6)->isoFormat('MMMM YYYY') . ' - ' . now()->isoFormat('MMMM YYYY'),
            default => 'Falsches Leistungsdatum',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function masterLineItems()
    {
        return $this->hasMany(MasterLineItem::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'customer_id',
        'user_id',
        'billing_frequency',
        'currency',
        'days_till_due',
        'status',
        'next_print',
        'total_without_tax',
        'total_with_tax',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'next_print' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
