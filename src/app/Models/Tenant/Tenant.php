<?php

namespace App\Models\Tenant;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\MasterInvoice;
use App\Models\Setting;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Tenant
 *
 * @todo: refactor that the id is 'key' and the slug can go
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $owner_id
 * @property User $owner
 * @property Collection<User> $users
 * @property Collection<Customer> $customers
 * @property string|null $legal_info_id
 * @property LegalInfo|null $currentLegalInfo
 * @property string|null $general_info_id
 * @property GeneralInfo|null $currentGeneralInfo
 * @property Collection<Setting> $settings
 */
class Tenant extends Model
{
    use HasFactory;
    use HasUuid;

    public $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'slug', 'owner_id', 'legal_info_id', 'general_info_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function currentLegalInfo(): BelongsTo
    {
        return $this->belongsTo(LegalInfo::class, 'legal_info_id', 'id');
    }

    public function currentGeneralInfo(): BelongsTo
    {
        return $this->belongsTo(GeneralInfo::class, 'general_info_id', 'id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Customer::class);
    }

    public function masterInvoices(): HasManyThrough
    {
        return $this->hasManyThrough(MasterInvoice::class, Customer::class);
    }


    public function setupErrors(): array
    {
        $errors = [];

        if ($this->currentLegalInfo === null) {
            $errors[] = __('tenants.errors.no_legal_infos');
        }

        if ($this->currentGeneralInfo === null) {
            $errors[] = __('tenants.errors.no_general_infos');
        }


        return $errors;
    }

    public function getSetting(string $key): null|int|float|string|bool
    {
        /** @var ?Setting $setting */
        $setting = $this->settings()->where('key', $key)->first();

        return $setting?->value;
    }

    /**
     * Return the tenant key for this tenant. Currently it is the same as the slug.
     *
     * @return string
     */
    public function getTenantKey(): string
    {
        return $this->slug;
    }


    /**
     * Route Helper for Blades
     *
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function route(string $name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, array_merge([$this->id], $parameters), $absolute);
    }
}
