<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterInvoiceActiveRequest;
use App\Http\Requests\MasterInvoiceStoreRequest;
use App\Http\Resources\V1\MasterInvoiceResource;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Services\MasterInvoices\MasterInvoiceService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class MasterInvoiceController extends Controller
{
    private readonly \App\Services\MasterInvoices\MasterInvoiceService $masterInvoiceService;

    public function __construct()
    {
        $this->masterInvoiceService = new MasterInvoiceService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Tenant $tenant)
    {
        return MasterInvoiceResource::collection(Auth::user()->masterInvoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MasterInvoiceStoreRequest $request, Tenant $tenant): \App\Http\Resources\V1\MasterInvoiceResource
    {
        $masterInvoice = $this->masterInvoiceService->store($request->validated());

        return $this->show($tenant, $masterInvoice);
    }

    public function active(MasterInvoiceActiveRequest $request, Tenant $tenant, MasterInvoice $masterInvoice): \App\Http\Resources\V1\MasterInvoiceResource
    {
        $this->masterInvoiceService->active($request->validated(), $masterInvoice);

        return $this->show($tenant, $masterInvoice);
    }

    public function pause(Tenant $tenant, MasterInvoice $masterInvoice): \App\Http\Resources\V1\MasterInvoiceResource
    {
        $this->masterInvoiceService->pause($masterInvoice);

        return $this->show($tenant, $masterInvoice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, MasterInvoice $masterInvoice): \App\Http\Resources\V1\MasterInvoiceResource
    {
        return new MasterInvoiceResource($masterInvoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant, MasterInvoice $masterInvoice): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $masterInvoice->delete();

        return response('', 204);
    }
}
