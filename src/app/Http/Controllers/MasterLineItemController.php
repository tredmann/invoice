<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterLineItemRequest;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Tenant\Tenant;
use App\Services\MasterLineItems\MasterLineItemService;

class MasterLineItemController extends Controller
{
    /**
     * @var MasterLineItemService
     */
    private $masterLineItemService;

    public function __construct(MasterLineItemService $masterLineItemService)
    {
        $this->masterLineItemService = $masterLineItemService;
    }

    public function create(Tenant $tenant, MasterInvoice $masterInvoice)
    {
        return view('default.master_line_items.create', ['masterInvoice' => $masterInvoice, 'tenant' => $tenant]);
    }

    public function store(MasterLineItemRequest $request, Tenant $tenant)
    {
        $masterLineItem = $this->masterLineItemService->store($request->validated());

        if ($request['submit'] === 'done') {
            return redirect(
                route('customers.masterInvoices', [
                    'customer' => $masterLineItem->masterInvoice->customer,
                    'tenant' => $tenant,
                ]),
            )->with(['success' => 'Rechnungsposition erfolgreich gespeichert!']);
        }

        if ($request['submit'] === 'continue') {
            return redirect(
                route('masterLineItems.create', [
                    'masterInvoice' => $masterLineItem->masterInvoice,
                    'tenant' => $tenant,
                ]),
            )->with(['success' => 'Rechnungsposition erfolgreich gespeichert!']);
        }
    }

    public function edit(Tenant $tenant, MasterLineItem $masterLineItem)
    {
        return view('default.master_line_items.edit', ['masterLineItem' => $masterLineItem, 'tenant' => $tenant]);
    }

    public function update(MasterLineItemRequest $request, Tenant $tenant, MasterLineItem $masterLineItem)
    {
        $this->masterLineItemService->update($request->validated(), $masterLineItem);

        return redirect(
            route('masterInvoices.masterLineItems', [
                'masterInvoice' => $masterLineItem->masterInvoice,
                'tenant' => $tenant,
            ]),
        )->with(['success' => 'Rechnungsposition erfolgreich aktualisiert!']);
    }

    public function destroy(Tenant $tenant, MasterLineItem $masterLineItem)
    {
        $masterInvoice = $masterLineItem->masterInvoice;
        $masterLineItem->delete();

        return redirect(
            route('masterInvoices.masterLineItems', [
                'masterInvoice' => $masterInvoice,
                'tenant' => $tenant,
            ]),
        )->with(['delete' => 'Rechnungsposition erfolgreich gelöscht!']);
    }
}
