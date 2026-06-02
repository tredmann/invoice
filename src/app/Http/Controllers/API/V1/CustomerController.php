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
    /**
     * @var CustomerService
     */
    private $customerService;

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
     *
     * @return CustomerResource|\Illuminate\Http\JsonResponse
     */
    public function store(CustomerRequest $request, Tenant $tenant)
    {
        $customer = $this->customerService->store($request->validated(), $tenant);

        return $this->show($tenant, $customer);
    }

    /**
     * Display the specified resource.
     *
     * @return CustomerResource
     */
    public function show(Tenant $tenant, Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return CustomerResource|\Illuminate\Http\JsonResponse
     */
    public function update(CustomerRequest $request, Tenant $tenant, Customer $customer)
    {
        $customer->update($request->validated());

        return $this->show($tenant, $customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Tenant $tenant, Customer $customer)
    {
        $customer->delete();

        return response('', 204);
    }
}
