<?php

namespace App\Routes\Tenant;

use App\Http\Controllers\Tenant\LegalInfoController;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'tenants/{tenant}',
        'middleware' => IdentifyTenant::class,
    ],
    function () {
        Route::group(
            [
                'prefix' => 'legalInfos',
                'as' => 'legalInfos.',
                'middleware' => ['auth', 'can:isOwnerViewGate,tenant'],
            ],
            function () {
                Route::get('/', [LegalInfoController::class, 'index'])->name('index');
                Route::delete('/{legalInfo}', [LegalInfoController::class, 'destroy'])->name('destroy');
                Route::patch('/{legalInfo}', [LegalInfoController::class, 'update'])->name('update');
                Route::get('/{legalInfo}/edit', [LegalInfoController::class, 'edit'])->name('edit');

                Route::group(
                    [
                        'middleware' => ['can:isFirstWithTenant,App\LegalInfo,tenant'],
                    ],
                    function () {
                        Route::post('/', [LegalInfoController::class, 'store'])->name('store');
                        Route::get('/create', [LegalInfoController::class, 'create'])->name('create');
                    },
                );
            },
        );
    },
);
