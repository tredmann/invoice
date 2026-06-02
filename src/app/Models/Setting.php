<?php

namespace App\Models;

use App\Enums\Settings;
use App\Models\Tenant\Tenant;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

/**
 * Class Setting
 *
 * @property string $id
 * @property string $tenant_id
 * @property string $key
 * @property string $value
 * @property string $type
 */
class Setting extends Model
{
    use HasUuid;
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    public const VALUE_TYPES = ['boolean', 'string', 'integer', 'float', self::VALUE_SECRET];

    public const VALUE_SECRET = 'secret';

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['tenant_id', 'key', 'value', 'type'];

    public function getValueAttribute(string $value): int|bool|string|float
    {
        $value = Crypt::decryptString($value);

        if ($this->type !== self::VALUE_SECRET) {
            settype($value, $this->type);
        }

        return $value;
    }

    public static function isEnabled(Tenant $tenant, Settings $setting): bool
    {
        $s = Setting::where('tenant_id', $tenant->id)->where('key', $setting->value)->first();

        if ($s === null) {
            return false;
        }

        return $s->value === true;
    }
}
