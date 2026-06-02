<?php

namespace App\Models;

use App\Models\Tenant\Tenant;
use App\Traits\HasUniqueNumber;
use App\Traits\HasUuid;
use App\Traits\TracksTenant;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Customer
 *
 * @property string $id
 * @property Collection<Invoice> $invoices
 * @property Collection<MasterInvoice> $masterInvoices
 * @property string $customer_no
 * @property int $user_id
 * @property User $user
 * @property string $tenant_id
 * @property Tenant $tenant
 * @property Collection<CustomerMailReceiver> $customerMailReceivers
 * @property string $company
 * @property string $name
 * @property string $street
 * @property string $postal
 * @property string $city
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $invoices_count
 * @property-read int|null $master_invoices_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 *
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;
    use TracksTenant;
    use HasUniqueNumber;

    protected $keyType = 'string';

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function masterInvoices()
    {
        return $this->hasMany(MasterInvoice::class);
    }

    public function customerMailReceivers()
    {
        return $this->hasMany(CustomerMailReceiver::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['customer_no', 'tenant_id', 'user_id', 'company', 'name', 'street', 'postal', 'city'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
