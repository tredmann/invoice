<?php

namespace App\Models;

use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property bool  $is_admin
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property LegalInfo $legalInfo
 * @property Collection<Customer> $customers
 * @property Collection<Invoice> $invoices
 * @property Collection<LineItem> $lineItems
 * @property Collection<MasterInvoice> $masterInvoices
 * @property Collection<MasterLineItem> $masterLineItems
 * @property Collection<Tenant> $tenants
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function legalInfo()
    {
        return $this->hasOne(LegalInfo::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

    public function masterInvoices()
    {
        return $this->hasMany(MasterInvoice::class);
    }

    public function masterLineItems()
    {
        return $this->hasMany(MasterLineItem::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)->withTimestamps();
    }

    public function getInitials(): string
    {
        return substr($this->name, 0, 2);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    #[\Override]
    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    #[\Override]
    protected $appends = ['profile_photo_url'];
}
