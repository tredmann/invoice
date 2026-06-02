<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LineItemRequest;
use App\Http\Resources\V1\LineItemResource;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\Tenant;
use App\Services\LineItems\LineItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class LineItemController extends Controller
{
    /**
     * @var LineItemService
     */
    private $lineItemService;

    public function __construct()
    {
        $this->lineItemService = new LineItemService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Tenant $tenant)
    {
        $user = Auth::user();

        return LineItemResource::collection($user->lineItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return LineItemResource
     */
    public function store(LineItemRequest $request, Tenant $tenant, Invoice $invoice)
    {
        $lineItem = $this->lineItemService->store($request->validated(), $invoice);

        return $this->show($tenant, $lineItem);
    }

    /**
     * Display the specified resource.
     *
     * @return LineItemResource
     */
    public function show(Tenant $tenant, LineItem $lineItem)
    {
        return new LineItemResource($lineItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return LineItemResource|JsonResponse
     */
    public function update(LineItemRequest $request, Tenant $tenant, LineItem $lineItem)
    {
        $lineItem->update($request->validated());

        return $this->show($tenant, $lineItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Tenant $tenant, LineItem $lineItem)
    {
        $lineItem->delete();

        return response('', 204);
    }
}
