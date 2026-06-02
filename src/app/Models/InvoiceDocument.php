<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceDocument
 *
 * @property string $id
 * @property string|null $invoice_id
 * @property Invoice $invoice
 * @property string $user_id
 * @property User $user
 * @property string $path
 * @property string $mime_type
 * @property string $storage
 * @property string $type
 */
class InvoiceDocument extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    public const TYPE_INVOICE_DOCUMENT = 'invoice_document';

    public const TYPE_ATTACHMENT = 'attachment';

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
    #[\Override]
    protected $fillable = [
        'user_id',
        'invoice_id',
        'path',
        'mime_type',
        'storage',
        'type',
    ];
}
