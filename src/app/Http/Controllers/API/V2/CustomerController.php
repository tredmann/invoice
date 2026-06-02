<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\V2\CustomerResource;
use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Services\API\V2\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display all resources.
     *
     * @return JsonResponse
     */
    public function index(Tenant $tenant)
    {
        $customers = $tenant->customers;

        $arr = CustomerResource::collection($customers);

        return response()->json($arr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(CustomerRequest $request, Tenant $tenant)
    {
        $customer = CustomerService::store($request, $tenant);

        return response()->json(new CustomerResource($customer));
    }

    /**
     * Display customer.
     */
    public function show(Tenant $tenant, Customer $customer): ?JsonResponse
    {
        $customer = $tenant->customers->find($customer->id);

        return $customer !== null ? response()->json(new CustomerResource($customer)) : null;
    }
}
