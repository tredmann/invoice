<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterLineItemRequest;
use App\Http\Resources\V1\MasterLineItemResource;
use App\Models\MasterLineItem;
use App\Models\Tenant\Tenant;
use App\Services\MasterLineItems\MasterLineItemService;

class MasterLineItemController extends Controller
{
    private readonly \App\Services\MasterLineItems\MasterLineItemService $masterLineItemService;

    public function __construct()
    {
        $this->masterLineItemService = new MasterLineItemService();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MasterLineItemRequest $request, Tenant $tenant): \App\Http\Resources\V1\MasterLineItemResource
    {
        $masterLineItem = $this->masterLineItemService->store($request->validated());

        return $this->show($tenant, $masterLineItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, MasterLineItem $masterLineItem): \App\Http\Resources\V1\MasterLineItemResource
    {
        return new MasterLineItemResource($masterLineItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MasterLineItemRequest $request, Tenant $tenant, MasterLineItem $masterLineItem): \App\Http\Resources\V1\MasterLineItemResource
    {
        $masterLineItem->update($request->validated());

        return $this->show($tenant, $masterLineItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Exception
     */
    public function destroy(Tenant $tenant, MasterLineItem $masterLineItem): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $masterLineItem->delete();

        return response('', 204);
    }
}
