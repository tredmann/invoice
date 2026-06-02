<?php

namespace App\Services\API\V2;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Tenant\Tenant;

class CustomerService
{
    public static function store(CustomerRequest $request, Tenant $tenant): Customer
    {
        $input = $request->validated();
        $customer = new Customer($input);
        $customer->tenant()->associate($tenant);
        $customer->save();

        return $customer->refresh();
    }
}
