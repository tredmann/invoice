<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V2\CustomerController;
use App\Http\Controllers\API\V2\InvoiceController;
use App\Http\Controllers\API\V2\TenantController;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    //Route::group(['prefix' => 'customers'], function () {
    //    Route::get('/{tenant}', [InvoiceController::class, 'allCustomers']);
    //    Route::get('/{customer}', [InvoiceController::class, 'showCustomer']);
    //});

    Route::group(['prefix' => 'tenant'], function () {
        Route::get('/', [TenantController::class, 'index'])->name('tenants.all');

        Route::group(['middleware' => IdentifyTenant::class], function () {
            Route::get('/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
            Route::post('/{tenant}/invoices', [InvoiceController::class, 'store'])->name('invoices.store.v2');
            Route::get('/{tenant}/invoices', [InvoiceController::class, 'getAllInvoicesByTenant'])->name('tenants-all-invoices');
            Route::get('/{tenant}/invoices/{invoice}', [InvoiceController::class, 'show'])->name('tenants.invoices.invoice');

            Route::get('/{tenant}/customers', [CustomerController::class, 'index'])->name('tenants.customers');
            Route::post('/{tenant}/customers', [CustomerController::class, 'store'])->name('tenants.customers.store');
            Route::get('{tenant}/customers/{customer}', [CustomerController::class, 'show'])->name('tenants.customers.show');
        });
    });

    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });
});
