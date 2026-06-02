<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V2\InvoiceRequest;
use App\Http\Resources\V2\InvoiceResource;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Services\API\V2\InvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Allowed status types = 'draft' + 'open'.
     *
     * @return JsonResponse
     */
    public function store(InvoiceRequest $request)
    {
        $invoice = InvoiceService::store($request);

        return response()->json(new InvoiceResource($invoice));
    }

    /**
     * Display the specified resource.
     *
     * Reachable via either the tenant-scoped route (where IdentifyTenant guards
     * cross-tenant access) or the bare /api/v2/invoices/{invoice} route. The
     * bare route must verify the current user belongs to the invoice's tenant.
     *
     * @return JsonResponse
     */
    public function show(Tenant $tenant, Invoice $invoice)
    {
        $tenantId = $invoice->customer->tenant_id;

        $user = request()->user();
        if ($user === null || ! $user->tenants()->where('tenants.id', $tenantId)->exists()) {
            abort(404);
        }

        return response()->json(new InvoiceResource($invoice));
    }

    /**
     * Display all resources.
     */
    public function getAllInvoicesByTenant(Tenant $tenant): array
    {
        $invoices = $tenant->invoices;

        $arr = $invoices->map(function ($invoice) {
            return new InvoiceResource($invoice);
        });

        return $arr->toArray();
    }
}
