<?php

namespace App\Http\Controllers;

use App\Enums\Settings;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\MasterInvoice;
use App\Models\Setting;
use App\Models\Tenant\Tenant;
use App\Services\Customers\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
    }

    public function index(Tenant $tenant): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $customers = $tenant->customers()->orderBy('company')->paginate(500);

        return view('default.customers.index', ['tenant' => $tenant, 'customers' => $customers]);
    }

    public function invoices(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $invoices = $customer->invoices;

        return view('default.customers.invoices', [
            'tenant' => $tenant,
            'customer' => $customer,
            'invoices' => $invoices->where('status', '!=', Invoice::STATUS_DRAFT)->sortByDesc('invoice_no'),
            'drafts' => $invoices->where('status', '=', Invoice::STATUS_DRAFT)->sortBy('updated_at'),
        ]);
    }

    public function masterInvoices(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $masterInvoices = $customer->masterInvoices;

        return view('default.customers.masterInvoices', [
            'customer' => $customer,
            'tenant' => $tenant,
            'masterInvoices' => $masterInvoices
                ->where('status', '!=', MasterInvoice::STATUS_DRAFT)
                ->sortByDesc('updated_at'),
            'drafts' => $masterInvoices->where('status', '=', MasterInvoice::STATUS_DRAFT)->sortByDesc('updated_at'),
        ]);
    }

    public function mailReceivers(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customers.mailReceivers', [
            'customer' => $customer,
            'tenant' => $tenant,
            'mailReceivers' => $customer->customerMailReceivers()->paginate(15),
        ]);
    }

    public function show(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customers.show', [
            'customer' => $customer,
            'tenant' => $tenant,
        ]);
    }

    public function create(Tenant $tenant): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customers.create', ['tenant' => $tenant]);
    }

    public function store(CustomerRequest $request, Tenant $tenant)
    {
        $validated = $request->validated();
        $validated['country'] ??= 'DE';
        $this->customerService->store($validated, $tenant);

        return redirect(route('customers.index', ['tenant' => $tenant]))->with('success', 'Kunden erfolgreich erstellt');
    }

    public function destroy(Tenant $tenant, Customer $customer)
    {
        $this->customerService->destroy($customer);

        return redirect(route('customers.index', ['tenant' => $tenant]))->with('success', 'Kunden erfolgreich gelöscht');
    }

    public function edit(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customers.edit', ['customer' => $customer, 'tenant' => $tenant]);
    }

    public function update(Request $request, Tenant $tenant, Customer $customer)
    {
        $validation = [
            'customer_no' => 'nullable|string',
            'company' => 'required|string|max:100',
            'name' => 'nullable|string|max:50',
            'street' => 'required|string|max:100',
            'postal' => 'required|string|max:5',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:2|in:DE',
        ];

        if (Setting::isEnabled($tenant, Settings::EDIT_CUSTOMER_ID)) {
            $validation['customer_no'] = 'required|string';
        }

        $validated = $request->validate($validation);
        $validated['country'] ??= 'DE';

        $this->customerService->update($validated, $customer, $tenant);

        return redirect(route('customers.index', ['tenant' => $tenant]))->with(
            'success',
            'Kunden erfolgreich aktualisiert',
        );
    }
}
