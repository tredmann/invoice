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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
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
     *
     * @return InvoiceResource|JsonResponse
     */
    public function store(InvoiceStoreRequest $request, Tenant $tenant)
    {
        $invoice = $this->invoiceService->store($request->validated());

        return $this->show($tenant, $invoice);
    }

    public function open(InvoiceStatusOpenRequest $request, Tenant $tenant, Invoice $invoice)
    {
        $this->invoiceService->open($invoice, $request->validated());

        return $this->show($tenant, $invoice);
    }

    public function paid(InvoiceStatusPaidRequest $request, Tenant $tenant, Invoice $invoice)
    {
        $validatedData = $request->validated();

        $this->invoiceService->paid($invoice, $validatedData['paid_at']);

        return $this->show($tenant, $invoice);
    }

    /**
     * Display the specified resource.
     *
     * @return InvoiceResource
     */
    public function show(Tenant $tenant, Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * @throws Exception
     */
    public function destroy(Tenant $tenant, Invoice $invoice)
    {
        $invoice->delete();

        return response('', 204);
    }
}
