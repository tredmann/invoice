<?php

namespace App\Http\Controllers;

use App\Http\Requests\LineItemRequest;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\Tenant;
use App\Services\LineItems\LineItemService;
use Illuminate\Support\Facades\Gate;

class LineItemController extends Controller
{
    /**
     * @var LineItemService
     */
    private $lineItemService;

    public function __construct(LineItemService $lineItemService)
    {
        $this->lineItemService = $lineItemService;
    }

    public function create(Tenant $tenant, Invoice $invoice)
    {
        Gate::authorize('stop-post-open-invoice-action', $invoice);

        return view('default.line_items.create', ['invoice' => $invoice, 'tenant' => $tenant]);
    }

    public function store(LineItemRequest $request, Tenant $tenant, Invoice $invoice)
    {
        $lineItem = $this->lineItemService->store($request->validated(), $invoice);

        if ($request['submit'] === 'done') {
            return redirect(
                route('customers.invoices', ['customer' => $lineItem->invoice->customer, 'tenant' => $tenant]),
            )->with(['success' => 'Rechnungsposition erfolgreich gespeichert!']);
        }

        if ($request['submit'] === 'continue') {
            return redirect(route('lineItems.create', ['invoice' => $lineItem->invoice, 'tenant' => $tenant]))->with([
                'success' => 'Rechnungsposition erfolgreich gespeichert!',
            ]);
        }
    }

    public function edit(Tenant $tenant, LineItem $lineItem)
    {
        Gate::authorize('stop-post-open-invoice-action', $lineItem->invoice);

        return view('default.line_items.edit', ['lineItem' => $lineItem, 'tenant' => $tenant]);
    }

    public function update(LineItemRequest $request, Tenant $tenant, LineItem $lineItem)
    {
        Gate::authorize('stop-post-open-invoice-action', $lineItem->invoice);

        $this->lineItemService->update($request->validated(), $lineItem);

        return redirect(route('invoices.show', ['invoice' => $lineItem->invoice, 'tenant' => $tenant]))->with([
            'success' => 'Rechnungsposition erfolgreich aktualisiert!',
        ]);
    }

    public function destroy(Tenant $tenant, LineItem $lineItem)
    {
        Gate::authorize('stop-post-open-invoice-action', $lineItem->invoice);

        $lineItem->delete();

        return redirect(route('invoices.show', ['invoice' => $lineItem->invoice, 'tenant' => $tenant]))->with([
            'success' => 'Rechnungsposition erfolgreich gelöscht!',
        ]);
    }
}
