<?php

namespace App\Routes\Admin;

use App\Http\Controllers\Tenant\GeneralInfoController;
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
                'prefix' => 'generalInfos',
                'as' => 'generalInfos.',
                'middleware' => ['auth', 'can:isOwnerViewGate,tenant'],
            ],
            function () {
                Route::get('/', [GeneralInfoController::class, 'index'])->name('index');
                Route::delete('/{generalInfo}', [GeneralInfoController::class, 'destroy'])->name('destroy');
                Route::patch('/{generalInfo}', [GeneralInfoController::class, 'update'])->name('update');
                Route::get('/{generalInfo}/edit', [GeneralInfoController::class, 'edit'])->name('edit');

                Route::group(
                    [
                        'middleware' => 'can:isFirst,App\GeneralInfo',
                    ],
                    function () {
                        Route::get('/create', [GeneralInfoController::class, 'create'])->name('create');
                        Route::post('/', [GeneralInfoController::class, 'store'])->name('store');
                    },
                );
            },
        );
    },
);
