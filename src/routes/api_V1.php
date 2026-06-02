<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\InvoiceController;
use App\Http\Controllers\API\V1\LineItemController;
use App\Http\Controllers\API\V1\MasterInvoiceController;
use App\Http\Controllers\API\V1\MasterLineItemController;
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

    Route::group(
        [
            'prefix' => '{tenant}',
            'middleware' => IdentifyTenant::class,
        ],
        function () {
            Route::group(['prefix' => 'customers'], function () {
                Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
                Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
                Route::get('/{customer}', [CustomerController::class, 'show'])->name('customers.show');
                Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
                Route::patch('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
            });

            Route::group(['prefix' => 'invoices'], function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
                Route::post('/', [InvoiceController::class, 'store'])->name('invoices.store');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.lineItems');
                Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
                Route::patch('/{invoice}/open', [InvoiceController::class, 'open'])->name('invoices.open');
                Route::patch('/{invoice}/paid', [InvoiceController::class, 'paid'])->name('invoices.paid');
            });

            Route::group(['prefix' => 'lineItems'], function () {
                Route::post('/{invoice}', [LineItemController::class, 'store'])->name('lineItems.store');
                Route::patch('/{lineItem}', [LineItemController::class, 'update'])->name('lineItems.update');
                Route::delete('/{lineItem}', [LineItemController::class, 'destroy'])->name('lineItems.destroy');
            });

            Route::group(['prefix' => 'masterInvoices'], function () {
                Route::post('/', [MasterInvoiceController::class, 'store'])->name('masterInvoices.store');
                Route::get('/{masterInvoice}', [MasterInvoiceController::class, 'show'])->name(
                    'masterInvoices.masterLineItems',
                );
                Route::delete('/{masterInvoice}', [MasterInvoiceController::class, 'destroy'])->name(
                    'masterInvoices.destroy',
                );
                Route::patch('/{masterInvoice}/active', [MasterInvoiceController::class, 'active'])->name(
                    'masterInvoices.active',
                );
                Route::patch('/{masterInvoice}/pause', [MasterInvoiceController::class, 'pause'])->name(
                    'masterInvoices.pause',
                );
            });

            Route::group(['prefix' => 'masterLineItems'], function () {
                Route::post('/', [MasterLineItemController::class, 'store'])->name('masterLineItems.store');
                Route::patch('/{masterLineItem}', [MasterLineItemController::class, 'update'])->name(
                    'masterLineItems.update',
                );
                Route::delete('/{masterLineItem}', [MasterLineItemController::class, 'destroy'])->name(
                    'masterLineItems.destroy',
                );
            });
        },
    );
});
