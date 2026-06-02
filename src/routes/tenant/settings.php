<?php

namespace App\Routes\Tenant;

use App\Http\Controllers\Tenant\SettingController;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'tenants/{tenant}/',
        'middleware' => IdentifyTenant::class,
    ],
    function () {
        Route::group(
            [
                'prefix' => 'settings',
                'as' => 'settings.',
                'middleware' => ['auth', 'can:isOwnerViewGate,tenant'],
            ],
            function () {
                Route::get('/', [SettingController::class, 'index'])->name('index');
                Route::get('/test-email-settings', [SettingController::class, 'testEmailSettingsForm'])->name('testEmailSettingsForm');
                Route::post('/test-email-settings', [SettingController::class, 'testEmailSettings'])->name('testEmailSettings');
                Route::post('/', [SettingController::class, 'store'])->name('store');
                Route::get('/create', [SettingController::class, 'create'])->name('create');
                Route::delete('/{setting}', [SettingController::class, 'destroy'])->name('destroy');
                Route::patch('/{setting}', [SettingController::class, 'update'])->name('update');
                Route::get('/{setting}/edit', [SettingController::class, 'edit'])->name('edit');
            },
        );
    },
);
