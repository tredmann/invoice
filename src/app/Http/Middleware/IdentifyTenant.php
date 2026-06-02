<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Setting;
use App\Models\Tenant\Tenant;
use Closure;
use Illuminate\Http\Request;
use View;

class IdentifyTenant
{
    /**
     * Per-resource tenant_id resolvers used for the cross-tenant guard.
     *
     * Each entry: route-param name => [model class, callable(model): ?string].
     *
     * Built lazily so first-class callable syntax (methodName(...)) can be used —
     * PHPStan otherwise flags the static methods as unused because string-pair
     * callables in a const array can't be statically traced to their targets.
     *
     * @return array<string, array{0: class-string, 1: callable}>
     */
    private static function resolvers(): array
    {
        return [
            'customer' => [Customer::class, self::customerTenantId(...)],
            'invoice' => [Invoice::class, self::invoiceTenantId(...)],
            'masterInvoice' => [MasterInvoice::class, self::masterInvoiceTenantId(...)],
            'lineItem' => [LineItem::class, self::lineItemTenantId(...)],
            'masterLineItem' => [MasterLineItem::class, self::masterLineItemTenantId(...)],
            'customerMailReceiver' => [CustomerMailReceiver::class, self::customerMailReceiverTenantId(...)],
            'setting' => [Setting::class, self::settingTenantId(...)],
        ];
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantParam = $request->route('tenant');

        if ($tenantParam === null) {
            return $next($request);
        }

        // SubstituteBindings only resolves {tenant} to a Tenant model when the
        // controller signature requires it. Some endpoints (e.g. V2 store) only
        // accept a FormRequest, leaving $tenantParam as the raw UUID string.
        // Resolve explicitly so the guard runs uniformly.
        $tenant = $tenantParam instanceof Tenant
            ? $tenantParam
            : Tenant::find($tenantParam);

        if ($tenant === null) {
            abort(404);
        }

        View::share('tenant', $tenant);

        $user = $request->user();
        if ($user === null || ! $tenant->users()->where('users.id', $user->id)->exists()) {
            abort(404);
        }

        foreach (self::resolvers() as $param => [$class, $resolver]) {
            $bound = $request->route($param);
            if ($bound instanceof $class && $resolver($bound) !== $tenant->id) {
                abort(404);
            }
        }

        if (! $tenantParam instanceof Tenant) {
            $route = $request->route();
            if ($route instanceof \Illuminate\Routing\Route) {
                $route->setParameter('tenant', $tenant);
            }
        }

        return $next($request);
    }

    private static function customerTenantId(Customer $customer): string
    {
        return $customer->tenant_id;
    }

    private static function invoiceTenantId(Invoice $invoice): string
    {
        return $invoice->customer->tenant_id;
    }

    private static function masterInvoiceTenantId(MasterInvoice $masterInvoice): string
    {
        return $masterInvoice->customer->tenant_id;
    }

    private static function lineItemTenantId(LineItem $lineItem): string
    {
        return $lineItem->invoice->customer->tenant_id;
    }

    private static function masterLineItemTenantId(MasterLineItem $masterLineItem): string
    {
        return $masterLineItem->masterInvoice->customer->tenant_id;
    }

    private static function customerMailReceiverTenantId(CustomerMailReceiver $receiver): string
    {
        return $receiver->customer->tenant_id;
    }

    private static function settingTenantId(Setting $setting): string
    {
        return $setting->tenant_id;
    }
}
