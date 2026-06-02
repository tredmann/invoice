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
    /**
     * @var MasterLineItemService
     */
    private $masterLineItemService;

    public function __construct()
    {
        $this->masterLineItemService = new MasterLineItemService();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return MasterLineItemResource|\Illuminate\Http\JsonResponse
     */
    public function store(MasterLineItemRequest $request, Tenant $tenant)
    {
        $masterLineItem = $this->masterLineItemService->store($request->validated());

        return $this->show($tenant, $masterLineItem);
    }

    /**
     * Display the specified resource.
     *
     * @return MasterLineItemResource
     */
    public function show(Tenant $tenant, MasterLineItem $masterLineItem)
    {
        return new MasterLineItemResource($masterLineItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return MasterLineItemResource|\Illuminate\Http\JsonResponse
     */
    public function update(MasterLineItemRequest $request, Tenant $tenant, MasterLineItem $masterLineItem)
    {
        $masterLineItem->update($request->validated());

        return $this->show($tenant, $masterLineItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Tenant $tenant, MasterLineItem $masterLineItem)
    {
        $masterLineItem->delete();

        return response('', 204);
    }
}
