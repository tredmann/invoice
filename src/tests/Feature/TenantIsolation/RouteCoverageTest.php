<?php

namespace Tests\Feature\TenantIsolation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Sentinel: fails if any in-scope route lacks isolation coverage.
 *
 * Scope rules (see inScope()):
 *   - Every authenticated API V1 + V2 endpoint, EXCEPT auth and the
 *     curated EXCLUDED_PATTERNS list (non-goals per the umbrella spec).
 *   - Every destructive web route (DELETE/PATCH/POST) that lives under
 *     a {tenant} prefix.
 *
 * "Covered" means the route's HTTP method + URI shows up in one of the
 * isolation tests' data providers (after applying the per-provider
 * prefix mapping in PROVIDER_PREFIXES).
 */
class RouteCoverageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Routes intentionally excluded from Phase 2.
     *
     * The umbrella spec scopes Phase 2 to ~70 endpoints and pushes everything
     * else (read-only web GETs, Datev OAuth, API token mgmt, Excel import,
     * SMTP test, Jetstream/Fortify auth) to follow-up specs.
     */
    private const EXCLUDED_PATTERNS = [
        '#^api/v[12]/(register|login|logout)$#',
        '#^api/v2/tenant$#',
        '#^_debugbar#',
        '#^_ignition#',
        '#^sanctum#',
        '#^datev#',
        '#^api-tokens#',
        '#^admin/#',
        '#^login$#',
        '#^logout$#',
        '#^forgot-password$#',
        '#^reset-password#',
        '#^two-factor-challenge$#',
        '#^user/#',
        '#^current-team#',
        '#^teams#',
        '#^livewire#',
        '#^\{locale\}/livewire#',
        '#^tenants$#',
        '#^tenants/create$#',
        '#^/?$#',
        // Phase 2 hard cap follow-ups:
        '#^\{tenant\}/dashboard$#',                                  // importCustomers POST (Excel import)
        '#^\{tenant\}/customers/\{customer\}$#',                     // PATCH update (validation-heavy, follow-up)
        '#^\{tenant\}/customerMailReceivers/create/\{customer\}$#',  // GET form, not destructive
        '#^\{tenant\}/invoices/\{customer\}$#',                      // POST create new invoice (follow-up)
        '#^\{tenant\}/masterInvoices/\{customer\}$#',                // POST create new master invoice (follow-up)
        '#^tenants/\{tenant\}$#',                                    // tenants.show/update/destroy
        '#^tenants/\{tenant\}/invite-user$#',
        '#^tenants/\{tenant\}/\{user\}/remove-user$#',
    ];

    /**
     * Map of data-provider method-name suffix => URI prefix to prepend when
     * comparing with Laravel's route table. Web providers carry no prefix.
     */
    private const PROVIDER_PREFIXES = [
        'apiV1Endpoints' => 'api/v1/',
        'apiV2Endpoints' => 'api/v2/',
        'destructiveWebEndpoints' => '',
    ];

    public function testEveryInScopeRouteIsCovered(): void
    {
        $covered = $this->collectCoveredRoutes();

        $missing = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            $method = collect($route->methods())->reject(fn ($m) => in_array($m, ['HEAD']))->first();

            if (! $this->inScope($uri, $method)) {
                continue;
            }

            $key = $method.' '.$uri;
            if (! in_array($key, $covered, true)) {
                $missing[] = $key;
            }
        }

        self::assertEmpty(
            $missing,
            "Phase 2 missing isolation coverage for:\n  ".implode("\n  ", $missing)
        );
    }

    private function inScope(string $uri, ?string $method): bool
    {
        if ($method === null) {
            return false;
        }

        foreach (self::EXCLUDED_PATTERNS as $pattern) {
            if (preg_match($pattern, $uri)) {
                return false;
            }
        }

        if (str_starts_with($uri, 'api/v1/') || str_starts_with($uri, 'api/v2/')) {
            return true;
        }

        return in_array($method, ['DELETE', 'PATCH', 'POST'], true)
            && str_contains($uri, '{tenant}');
    }

    /** @return string[] */
    private function collectCoveredRoutes(): array
    {
        $covered = [];

        foreach ($this->isolationTestClasses() as $class) {
            foreach (self::PROVIDER_PREFIXES as $providerName => $prefix) {
                if (! method_exists($class, $providerName)) {
                    continue;
                }

                foreach ($class::$providerName() as [$httpMethod, $uriTemplate]) {
                    $covered[] = $httpMethod.' '.$prefix.ltrim($uriTemplate, '/');
                }
            }
        }

        return $covered;
    }

    /** @return class-string[] */
    private function isolationTestClasses(): array
    {
        return [
            InvoiceIsolationTest::class,
            MasterInvoiceIsolationTest::class,
            CustomerIsolationTest::class,
            LineItemIsolationTest::class,
            MasterLineItemIsolationTest::class,
            SettingIsolationTest::class,
            GeneralInfoIsolationTest::class,
            LegalInfoIsolationTest::class,
            CustomerMailReceiverIsolationTest::class,
        ];
    }
}
