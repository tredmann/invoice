<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function dashboard(Tenant $tenant): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('dashboard.dashboard', [
            'paidInvoices' => $tenant
                ->invoices()
                ->with('customer')
                ->where('status', '=', Invoice::STATUS_PAID)
                ->orderByDesc('paid_at')
                ->orderByDesc('updated_at')
                ->paginate(10),
            'openInvoices' => $tenant
                ->invoices()
                ->with('customer')
                ->where('status', '=', Invoice::STATUS_OPEN)
                ->orderBy('date_due')
                ->get(),
            'overdueInvoices' => $tenant
                ->invoices()
                ->with('customer')
                ->where('status', '=', Invoice::STATUS_OVERDUE)
                ->orderBy('date_due')
                ->get(),
            'masterInvoicesSentWithin30Days' => $tenant
                ->masterInvoices()
                ->with('customer')
                ->whereBetween('next_print', [now(), now()->addMonth()])
                ->orderBy('next_print')
                ->get(),
            'tenant' => $tenant,
        ]);
    }

    public function importCustomersView(Tenant $tenant): View
    {
        return view('dashboard.importCustomers', [
            'tenant' => $tenant,
        ]);
    }

    public function importCustomersStore(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($request->hasFile('excel_file')) {
            // Read the properties and data from the Excel file
            $properties = Excel::import(new CustomerImport($tenant), $request->file('excel_file'));

            return redirect(route('customers.index', ['tenant' => $tenant]))->with(
                'success',
                'Kunden erfolgreich aktualisiert',
            );
        }

        return redirect(route('import.customers.view', ['tenant' => $tenant]))->with(
            'error',
            'Sie haben die Kunden nicht hochgeladen, bitte versuchen Sie es erneut.',
        );
    }
}
