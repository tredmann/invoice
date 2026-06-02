<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Tenant::class => TenantPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('stop-post-open-invoice-action', function ($user, Invoice $invoice) {
            if ($invoice->status !== Invoice::STATUS_DRAFT) {
                throw new AuthorizationException('Diese Rechnung ist kein Entwurf mehr.');
            }

            return true;
        });
    }
}
