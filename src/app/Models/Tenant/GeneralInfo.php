<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeneralInfo
 *
 * @property string $id
 * @property string $name
 * @property string $owner
 * @property string $additional_address
 * @property string $street
 * @property string $postal
 * @property string $city
 * @property string $country
 * @property string $fax
 * @property string $email
 * @property string $homepage
 */
class GeneralInfo extends Model
{
    use HasFactory;
    use HasUuid;

    public $keyType = 'string';

    public $incrementing = false;

    public const COUNTRIES = [self::GERMANY];

    public const GERMANY = 'Deutschland';

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'owner',
        'additional_address',
        'street',
        'postal',
        'city',
        'country',
        'fax',
        'email',
        'homepage',
    ];
}
