<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStatusOpenRequest;
use App\Http\Requests\InvoiceStatusPaidRequest;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Services\Invoices\InvoiceService;
use Exception;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Tenant $tenant)
    {
        return InvoiceResource::collection(Auth::user()->invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceStoreRequest $request, Tenant $tenant): \App\Http\Resources\V1\InvoiceResource
    {
        $invoice = $this->invoiceService->store($request->validated());

        return $this->show($tenant, $invoice);
    }

    public function open(InvoiceStatusOpenRequest $request, Tenant $tenant, Invoice $invoice): \App\Http\Resources\V1\InvoiceResource
    {
        $this->invoiceService->open($invoice, $request->validated());

        return $this->show($tenant, $invoice);
    }

    public function paid(InvoiceStatusPaidRequest $request, Tenant $tenant, Invoice $invoice): \App\Http\Resources\V1\InvoiceResource
    {
        $validatedData = $request->validated();

        $this->invoiceService->paid($invoice, $validatedData['paid_at']);

        return $this->show($tenant, $invoice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, Invoice $invoice): \App\Http\Resources\V1\InvoiceResource
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Tenant $tenant, Invoice $invoice): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $invoice->delete();

        return response('', 204);
    }
}
