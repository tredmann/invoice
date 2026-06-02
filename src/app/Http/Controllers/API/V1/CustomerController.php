<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use App\Models\Customer;
use App\Models\Tenant\Tenant;
use App\Services\Customers\CustomerService;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private readonly \App\Services\Customers\CustomerService $customerService;

    public function __construct()
    {
        $this->customerService = new CustomerService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Tenant $tenant)
    {
        $user = Auth::user();

        return CustomerResource::collection($user->customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request, Tenant $tenant): \App\Http\Resources\V1\CustomerResource
    {
        $customer = $this->customerService->store($request->validated(), $tenant);

        return $this->show($tenant, $customer);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, Customer $customer): \App\Http\Resources\V1\CustomerResource
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Tenant $tenant, Customer $customer): \App\Http\Resources\V1\CustomerResource
    {
        $customer->update($request->validated());

        return $this->show($tenant, $customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Tenant $tenant, Customer $customer): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $customer->delete();

        return response('', 204);
    }
}
