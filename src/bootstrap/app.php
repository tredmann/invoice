<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\ConvertRequestFieldsToSnakeCase;
use App\Http\Middleware\ConvertResponseFieldsToCamelCase;
use App\Http\Middleware\IsOwner;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->as('api.v1.')
                ->group(base_path('routes/api_V1.php'));

            Route::middleware('api')
                ->prefix('api/v2')
                ->as('api.v2.')
                ->group(base_path('routes/api_V2.php'));

            Route::middleware('web')->group(base_path('routes/tenant/settings.php'));
            Route::middleware('web')->group(base_path('routes/tenant/legalInfos.php'));
            Route::middleware('web')->group(base_path('routes/tenant/generalInfos.php'));
            Route::middleware('web')->group(base_path('routes/tenant/tenants.php'));
            Route::prefix('admin')
                ->as('admin.')
                ->middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Replace L11 defaults with the project's customized variants.
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\TrimStrings::class, TrimStrings::class);
        $middleware->replace(\Illuminate\Http\Middleware\TrustProxies::class, TrustProxies::class);

        // Web group additions (Jetstream session authentication).
        $middleware->web(append: [
            \Laravel\Jetstream\Http\Middleware\AuthenticateSession::class,
        ]);

        // API group additions (throttle + project request/response field casing transforms).
        $middleware->throttleApi();
        $middleware->api(append: [
            ConvertRequestFieldsToSnakeCase::class,
            ConvertResponseFieldsToCamelCase::class,
        ]);

        // Route middleware aliases.
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'ownership' => IsOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // No project-specific reportable / renderable hooks at present.
    })
    ->create();
