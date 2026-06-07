<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Tenant\Tenant;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel
{
    public function __construct(private readonly Tenant $tenant)
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // check if the row is empty, and if it is empty, return "", otherwise return the " " (space)
        $spaceCompany = $row[6] !== null ? ' ' : '';
        $spaceName = $row[8] !== null ? ' ' : '';
        $company = $row[6] . $spaceCompany . $row[5];
        $name = $row[8] . $spaceName . $row[7];
        $street = $row[9];
        $postal = $row[10];
        $city = $row[11];

        // check if the one of the three fields is null, return null, otherwise return the value
        if ($street === null || $postal === null || $city === null) {
            return null;
        }

        // check if the company and name are "", return null, otherwise return the value
        if ($company === '' && $name === '') {
            return null;
        }

        // check if the company duplicated, return null, otherwise return the value
        if (Customer::where('company', $company)->exists()) {
            return null;
        }

        return new Customer([
            'tenant_id' => $this->tenant->id,
            'company' => $company,
            'name' => $name,
            'street' => $street,
            'postal' => $postal,
            'city' => $city,
            'country' => 'DE',
        ]);
    }
}
