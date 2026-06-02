<?php

namespace App\Routes\Tenant;

use App\Http\Controllers\Tenant\TenantController;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'tenants',
        'as' => 'tenants.',
        'middleware' => ['auth'],
    ],
    function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::post('/', [TenantController::class, 'store'])->name('store');
        Route::get('/create', [TenantController::class, 'create'])->name('create');
    },
);

Route::group(
    [
        'prefix' => 'tenants/{tenant}',
        'as' => 'tenants.',
        'middleware' => ['auth', IdentifyTenant::class, 'can:isOwnerViewGate,tenant'],
    ],
    function () {
        Route::delete('/', [TenantController::class, 'destroy'])->name('destroy');
        Route::patch('/', [TenantController::class, 'update'])->name('update');
        Route::get('/edit', [TenantController::class, 'edit'])->name('edit');
        Route::get('/', [TenantController::class, 'show'])->name('show');
        Route::get('/users', [TenantController::class, 'users'])->name('users');
        Route::get('/invite-user', [TenantController::class, 'inviteUserForm'])->name('invite-user-form');
        Route::patch('/invite-user', [TenantController::class, 'inviteUser'])->name('invite-user');
        Route::patch('/{user}/remove-user', [TenantController::class, 'removeUser'])->name('remove-user');
    },
);
