<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegalInfo
 *
 * @property string $id
 * @property string $registry_court
 * @property string $registry_no
 * @property string $company_owner
 * @property string $tax_no
 * @property string $vat_no
 * @property string $swift_bic
 * @property string $iban
 * @property string $bank_institute
 */
class LegalInfo extends Model
{
    use HasFactory;
    use HasUuid;

    protected $keyType = 'string';

    public $incrementing = false;

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
        'registry_court',
        'registry_no',
        'company_owner',
        'tax_no',
        'vat_no',
        'swift_bic',
        'iban',
        'bank_institute',
    ];
}
