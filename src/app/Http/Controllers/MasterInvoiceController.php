<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterInvoiceActiveRequest;
use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Services\MasterInvoices\MasterInvoiceService;

class MasterInvoiceController extends Controller
{
    /**
     * @var MasterInvoiceService
     */
    private $masterInvoiceService;

    public function __construct(MasterInvoiceService $masterInvoiceService)
    {
        $this->masterInvoiceService = $masterInvoiceService;
    }

    public function masterLineItems(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        return view('default.master_invoices.masterLineItems', [
            'masterInvoice' => $masterInvoice,
            'masterLineItems' => $masterInvoice->masterLineItems,
            'tenant' => $tenant,
        ]);
    }

    public function store(Tenant $tenant, Customer $customer)
    {
        $masterInvoice = MasterInvoice::create(['customer_id' => $customer->id]);

        return redirect(route('masterLineItems.create', ['masterInvoice' => $masterInvoice, 'tenant' => $tenant]));
    }

    public function activate(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        return view('default.master_invoices.activate', [
            'masterInvoice' => $masterInvoice,
            'tenant' => $tenant,
        ]);
    }

    public function active(MasterInvoiceActiveRequest $request, Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $this->masterInvoiceService->active($request->validated(), $masterInvoice);

        return redirect(
            route('customers.masterInvoices', ['customer' => $masterInvoice->customer, 'tenant' => $tenant]),
        )->with('success', 'Abo-Rechnung ist aktiv!');
    }

    public function pause(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $this->masterInvoiceService->pause($masterInvoice);

        return redirect(
            route('customers.masterInvoices', ['customer' => $masterInvoice->customer, 'tenant' => $tenant]),
        )->with('success', 'Abo-Rechnung wird pausiert!');
    }

    public function destroy(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        $masterInvoice->delete();

        return redirect(
            route('customers.masterInvoices', ['customer' => $masterInvoice->customer, 'tenant' => $tenant]),
        )->with('delete', 'Abo-Rechnung wurde gelöscht!');
    }
}
