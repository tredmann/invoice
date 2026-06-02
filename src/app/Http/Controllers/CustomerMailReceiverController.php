<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerMailReceiverRequest;
use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Tenant\Tenant;

class CustomerMailReceiverController extends Controller
{
    public function create(Tenant $tenant, Customer $customer): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customerMailReceivers.create', [
            'customer' => $customer,
            'tenant' => $tenant,
        ]);
    }

    public function store(CustomerMailReceiverRequest $request, Tenant $tenant)
    {
        $customerMailReceiver = CustomerMailReceiver::create($request->validated());

        return redirect(
            route('customers.mailReceivers', ['customer' => $customerMailReceiver->customer, 'tenant' => $tenant]),
        )->with('success', 'Email-Empfänger erfolgreich hinzugefügt!');
    }

    public function edit(Tenant $tenant, CustomerMailReceiver $customerMailReceiver): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('default.customerMailReceivers.edit', [
            'customerMailReceiver' => $customerMailReceiver,
            'tenant' => $tenant,
        ]);
    }

    public function update(CustomerMailReceiverRequest $request, Tenant $tenant, CustomerMailReceiver $customerMailReceiver)
    {
        $validatedData = $request->validated();

        $customerMailReceiver->update($validatedData);

        return redirect(
            route('customers.mailReceivers', ['customer' => $customerMailReceiver->customer, 'tenant' => $tenant]),
        )->with('success', 'Email-Empfänger erfolgreich aktualisiert!');
    }

    public function destroy(Tenant $tenant, CustomerMailReceiver $customerMailReceiver)
    {
        $customerMailReceiver->delete();

        return redirect(
            route('customers.mailReceivers', ['tenant' => $tenant, 'customer' => $customerMailReceiver->customer]),
        )->with('delete', 'Email-Empfänger erfolgreich gelöscht!');
    }
}
