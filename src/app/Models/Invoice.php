<?php

namespace App\Models;

use App\Interfaces\InvoiceInterface;
use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class Invoice
 *
 * @property string $id
 * @property User $user
 * @property Customer $customer
 * @property Collection<LineItem> $lineItems
 * @property Collection<InvoiceDocument> $documents
 * @property Invoice|null $cancelledInvoice
 * @property string $invoice_no
 * @property string $customer_id
 * @property int $user_id
 * @property string $performed_when
 * @property int $days_till_due
 * @property Carbon|null $date_due
 * @property string $status
 * @property string $mail_status
 * @property Carbon $paid_at
 * @property Carbon $open_at
 * @property string|null $currency
 * @property int $total_without_tax
 * @property int $total_with_tax
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 *
 * @mixin \Eloquent
 */
class Invoice extends Model implements InvoiceInterface
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_OPEN = 'open';

    public const STATUS_PAID = 'paid';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_OPEN_PDF_ERROR = 'open pdf error';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_CANCELLATION_INVOICE = 'cancellation invoice';

    public const MAIL_STATUS_NOT_MAILABLE = 'not mailable';

    public const MAIL_STATUS_MAILABLE = 'mailable';

    public const MAIL_STATUS_MAILING = 'mailing';

    public const MAIL_STATUS_MAILED = 'mailed';

    public const MAIL_STATUS_ERROR = 'mail error';

    public const DAYS_TILL_DUE = [7, 14, 30];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lineItems()
    {
        return $this->status !== self::STATUS_CANCELLATION_INVOICE ? $this->hasMany(LineItem::class) : $this->cancelledInvoice->lineItems();
    }

    public function documents()
    {
        return $this->hasMany(InvoiceDocument::class);
    }

    public function cancelledInvoice(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cancelled_invoice_id', 'id', 'invoices');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'user_id',
        'performed_when',
        'days_till_due',
        'currency',
        'date_due',
        'status',
        'mail_status',
        'paid_at',
        'open_at',
        'total_without_tax',
        'total_with_tax',
        'cancelled_invoice_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'date_due' => 'date',
            'updated_at' => 'datetime',
            'paid_at' => 'datetime',
            'open_at' => 'date',
        ];
    }

    #[\Override]
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    public function getInvoiceNumber(): string
    {
        return $this->invoice_no;
    }

    public function getInvoiceDate(): \DateTimeImmutable
    {
        return $this->updated_at->toDateTimeImmutable();
    }

    public function getInvoiceDocument(): ?InvoiceDocument
    {
        return $this->documents->where('type', InvoiceDocument::TYPE_INVOICE_DOCUMENT)->first();
    }

    public function getAttachment(): ?InvoiceDocument
    {
        return $this->documents->where('type', InvoiceDocument::TYPE_ATTACHMENT)->first();
    }

    public function getTaxDistribution(): array
    {
        $dist = [];

        foreach ($this->lineItems as $lineItem) {
            $appliedTax = $lineItem->with_tax - $lineItem->without_tax;
            $taxKey = (int)($lineItem->tax_rate * 100);
            if (!array_key_exists($taxKey, $dist)) {
                $dist[$taxKey] = [
                    'without_tax' => 0,
                    'tax' => 0,
                ];
            }

            $dist[$taxKey]['tax'] += $appliedTax;
            $dist[$taxKey]['without_tax'] += $lineItem->without_tax;
        }

        return $dist;
    }
}
