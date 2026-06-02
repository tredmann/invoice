<?php

namespace Tests\Concerns;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

trait CrossTenantMatrix
{
    /**
     * Asserts that the given endpoint rejects a request from a user that does not
     * belong to the tenant whose resource is being accessed.
     *
     * The caller is responsible for authenticating the "wrong" user via $this->be(...)
     * BEFORE invoking this assertion.
     *
     * Acceptable rejection statuses are 401, 403, and 404 - the app uses 404 for most
     * cross-tenant lookups and 403 for owner-only gated routes. Both are accepted.
     *
     * @param  string         $method      HTTP verb (case-insensitive).
     * @param  callable       $urlBuilder  Returns the URL string at call time.
     * @param  callable|null  $payload     Optional. Returns request body. Used by Phase 2 tests
     *                                     that inject a foreign tenant_id into the request body.
     */
    protected function assertEndpointRejectsCrossTenant(
        string $method,
        callable $urlBuilder,
        ?callable $payload = null
    ): TestResponse {
        $url = $urlBuilder();
        $body = $payload ? $payload() : [];

        $response = match (strtoupper($method)) {
            'GET' => $this->get($url),
            'POST' => $this->post($url, $body),
            'PUT' => $this->put($url, $body),
            'PATCH' => $this->patch($url, $body),
            'DELETE' => $this->delete($url, $body),
            default => throw new \InvalidArgumentException("Unsupported method: $method"),
        };

        // assertContains(needle, haystack) — needle is the actual status code, haystack is the allowed set.
        Assert::assertContains(
            $response->getStatusCode(),
            [401, 403, 404],
            sprintf(
                'Expected cross-tenant request %s %s to be rejected (401/403/404), got %d',
                $method,
                $url,
                $response->getStatusCode()
            )
        );

        return $response;
    }
}
