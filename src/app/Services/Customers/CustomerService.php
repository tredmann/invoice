<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Tenant\Tenant;

class CustomerService
{
    public function store(array $attributes, Tenant $tenant): Customer
    {
        $attributes['tenant_id'] = $tenant->id;

        return Customer::create($attributes);
    }

    public function destroy(Customer $customer): void
    {
        Customer::destroy($customer->id);
    }

    public function update(array $attributes, Customer $customer, Tenant $tenant): Customer
    {
        $attributes['tenant_id'] = $tenant->id;
        $customer->update($attributes);

        return $customer;
    }

    public function toArray($customer): array
    {
        return [
            'id' => $customer->id,
            'customerNo' => $customer->customer_no,
            'tenant' => [
                'id' => $customer->tenant->id,
                'name' => $customer->tenant->name,
                'slug' => $customer->tenant->slug,
            ],
            'company' => $customer->company,
            'name' => $customer->name,
            'street' => $customer->street,
            'postal' => $customer->postal,
            'city' => $customer->city,
        ];
    }
}
