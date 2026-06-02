<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerMailAddress
 *
 * @property string $id
 * @property string $user_id
 * @property User $user
 * @property string $customer_id
 * @property Customer $customer
 * @property string $email
 * @property string $gender
 * @property string $first_name
 * @property string $last_name
 */
class CustomerMailReceiver extends Model
{
    use HasFactory;
    use HasUuid;
    use TracksUser;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    public const GENDER = ['Herr', 'Frau', 'Divers'];

    public const MALE = 'Herr';

    public const FEMALE = 'Frau';

    public const DIVERSE = 'Divers';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getSalutation(): string
    {
        if ($this->gender === self::MALE) {
            return __('invoices.mail.salute.male', ['first_name' => $this->first_name, 'last_name' => $this->last_name]);
        }

        if ($this->gender === self::FEMALE) {
            return __('invoices.mail.salute.female', [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]);
        }

        if ($this->gender === self::DIVERSE) {
            return __('invoices.mail.salute.diverse', [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]);
        }

        return __('invoices.mail.salute.company', ['company' => $this->customer->company]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['user_id', 'customer_id', 'email', 'gender', 'first_name', 'last_name'];
}
