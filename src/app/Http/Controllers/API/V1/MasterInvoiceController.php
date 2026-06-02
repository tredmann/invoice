<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterInvoiceActiveRequest;
use App\Http\Requests\MasterInvoiceStoreRequest;
use App\Http\Resources\V1\MasterInvoiceResource;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Services\MasterInvoices\MasterInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class MasterInvoiceController extends Controller
{
    /**
     * @var MasterInvoiceService
     */
    private $masterInvoiceService;

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
     *
     * @return MasterInvoiceResource|JsonResponse
     */
    public function store(MasterInvoiceStoreRequest $request, Tenant $tenant)
    {
        $masterInvoice = $this->masterInvoiceService->store($request->validated());

        return $this->show($tenant, $masterInvoice);
    }

    public function active(MasterInvoiceActiveRequest $request, Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $this->masterInvoiceService->active($request->validated(), $masterInvoice);

        return $this->show($tenant, $masterInvoice);
    }

    public function pause(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $this->masterInvoiceService->pause($masterInvoice);

        return $this->show($tenant, $masterInvoice);
    }

    /**
     * Display the specified resource.
     *
     * @return MasterInvoiceResource
     */
    public function show(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        return new MasterInvoiceResource($masterInvoice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $masterInvoice->delete();

        return response('', 204);
    }
}
