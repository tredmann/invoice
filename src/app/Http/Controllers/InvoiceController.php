<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\OpenInvoiceAction;
use App\Actions\Invoice\SendMailAction;
use App\Http\Requests\InvoiceStatusOpenRequest;
use App\Http\Requests\InvoiceStatusPaidRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Services\Invoices\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function index(Request $request, Tenant $tenant)
    {
        $invoices = $tenant->invoices()
            // ->where('status', '<>', Invoice::STATUS_DRAFT)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('default.invoices.index', [
            'invoices' => $invoices,
            'tenant' => $tenant,
        ]);
    }

    public function store(Tenant $tenant, Customer $customer)
    {
        $invoice = Invoice::create(['customer_id' => $customer->id]);

        return redirect(route('lineItems.create', ['invoice' => $invoice, 'tenant' => $tenant]));
    }

    public function show(Tenant $tenant, Invoice $invoice)
    {
        return view('default.invoices.show', [
            'invoice' => $invoice,
            'tenant' => $tenant,
        ]);
    }

    public function pdf(Tenant $tenant, Invoice $invoice)
    {
        return $this->invoiceService->pdf($invoice);
    }

    public function sendmail(Request $request, Tenant $tenant, Invoice $invoice)
    {
        if ((new SendMailAction($invoice, $request))->fails()) {
            return redirect()->back();
        }

        return redirect()->back()->with(
            'success',
            'Rechnung wird versendet!',
        );
    }

    public function destroy(Tenant $tenant, Invoice $invoice)
    {
        Gate::authorize('stop-post-open-invoice-action', $invoice);

        $this->invoiceService->destroy($invoice);

        return redirect()->back()->with(
            'delete',
            'Rechnung erfolgreich gelöscht!',
        );
    }

    public function conclude(Tenant $tenant, Invoice $invoice)
    {
        return view('default.invoices.conclude', [
            'tenant' => $tenant,
            'invoice' => $invoice,
        ]);
    }

    public function open(InvoiceStatusOpenRequest $request, Tenant $tenant, Invoice $invoice)
    {
        if ((new OpenInvoiceAction($invoice, $request))->fails()) {
            return redirect()->back()->with('error', 'Rechnung konnte nicht eröffnet werden!');
        }

        return redirect(route('customers.invoices', ['customer' => $invoice->customer, 'tenant' => $tenant]))->with(
            'success',
            'Rechnung wird gespeichert und dann eröffnet!',
        );
    }

    public function paid(InvoiceStatusPaidRequest $request, Tenant $tenant, Invoice $invoice)
    {
        $validatedData = $request->validated();

        $this->invoiceService->paid($invoice, $validatedData['paid_at']);

        return redirect()->back()->with(
            'success',
            'Rechnung als "Bezahlt" markiert!',
        );
    }

    public function cancel(Tenant $tenant, Invoice $invoice)
    {
        $this->invoiceService->cancel($invoice);

        return redirect()->back()->with(
            'success',
            'Stornierungsrechnung wird ausgestellt und ein PDF gespeichert!'
        );
    }
}
