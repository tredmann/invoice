<?php

namespace Tests;

use App\Services\InvoiceDocuments\ZugferdService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind a passthrough ZugferdService by default so tests that dispatch
        // GeneratePDF with fake/empty PDF bytes do not fail on ZUGFeRD embedding.
        // Individual tests that want the real ZugferdService must resolve it
        // explicitly (e.g. app(ZugferdService::class) after binding their own).
        $this->app->bind(ZugferdService::class, function () {
            $stub = $this->createMock(ZugferdService::class);
            $stub->method('embed')->willReturnArgument(1);

            return $stub;
        });
    }
}
