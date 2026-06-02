<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMailReceiverController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Datev\ConnectController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LineItemController;
use App\Http\Controllers\MasterInvoiceController;
use App\Http\Controllers\MasterLineItemController;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['prefix' => 'datev'], function () {
    Route::get('/login', [ConnectController::class, 'login'])->name('datev.login');
    Route::get('/auth', [ConnectController::class, 'authorize'])->name('datev.authorize');
});

Route::middleware(['auth'])->group(function () {
    Route::group(
        [
            'prefix' => '/{tenant}',
            'middleware' => IdentifyTenant::class,
        ],
        function () {
            Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/dashboard/importCustomers', [DashboardController::class, 'importCustomersView'])->name('import.customers.view');
            Route::post('/dashboard', [DashboardController::class, 'importCustomersStore'])->name('import.customers.store');

            Route::group(['as' => 'customers.', 'prefix' => 'customers'], function () {
                Route::get('/', [CustomerController::class, 'index'])->name('index');
                Route::post('/', [CustomerController::class, 'store'])->name('store');
                Route::get('/create', [CustomerController::class, 'create'])->name('create');
                Route::get('/{customer}/invoices', [CustomerController::class, 'invoices'])->name('invoices');
                Route::get('/{customer}/masterInvoices', [CustomerController::class, 'masterInvoices'])->name(
                    'masterInvoices',
                );
                Route::get('/{customer}/mailReceivers', [CustomerController::class, 'mailReceivers'])->name(
                    'mailReceivers',
                );
                Route::get('/{customer}/show', [CustomerController::class, 'show'])->name('show');
                Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
                Route::patch('/{customer}', [CustomerController::class, 'update'])->name('update');
                Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            });

            Route::group(
                [
                    'prefix' => 'customerMailReceivers',
                ],
                function () {
                    Route::post('/', [CustomerMailReceiverController::class, 'store'])->name(
                        'customerMailReceivers.store',
                    );
                    Route::get('/create/{customer}', [CustomerMailReceiverController::class, 'create'])->name(
                        'customerMailReceivers.create',
                    );
                    Route::delete('/{customerMailReceiver}', [CustomerMailReceiverController::class, 'destroy'])->name(
                        'customerMailReceivers.destroy',
                    );
                    Route::patch('/{customerMailReceiver}', [CustomerMailReceiverController::class, 'update'])->name(
                        'customerMailReceivers.update',
                    );
                    Route::get('/{customerMailReceiver}/edit', [CustomerMailReceiverController::class, 'edit'])->name(
                        'customerMailReceivers.edit',
                    );
                },
            );

            Route::group(['prefix' => 'invoices'], function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
                Route::post('/{customer}', [InvoiceController::class, 'store'])->name('invoices.store');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
                Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
                Route::get('/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
                Route::post('/{invoice}/sendmail', [InvoiceController::class, 'sendmail'])->name('invoices.sendmail');
                Route::get('/{invoice}/conclude', [InvoiceController::class, 'conclude'])->name('invoices.conclude');
                Route::patch('/{invoice}/open', [InvoiceController::class, 'open'])->name('invoices.open');
                Route::patch('/{invoice}/paid', [InvoiceController::class, 'paid'])->name('invoices.paid');
                Route::patch('/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
            });

            Route::group(['prefix' => 'lineItems'], function () {
                Route::post('/{invoice}', [LineItemController::class, 'store'])->name('lineItems.store');
                Route::patch('/{lineItem}', [LineItemController::class, 'update'])->name('lineItems.update');
                Route::delete('/{lineItem}', [LineItemController::class, 'destroy'])->name('lineItems.destroy');
                Route::get('/{invoice}/create', [LineItemController::class, 'create'])->name('lineItems.create');
                Route::get('/{lineItem}/edit', [LineItemController::class, 'edit'])->name('lineItems.edit');
            });

            Route::group(
                [
                    'prefix' => 'masterInvoices',
                ],
                function () {
                    Route::post('/{customer}', [MasterInvoiceController::class, 'store'])->name('masterInvoices.store');
                    Route::get('/{masterInvoice}', [MasterInvoiceController::class, 'masterLineItems'])->name(
                        'masterInvoices.masterLineItems',
                    );
                    Route::delete('/{masterInvoice}', [MasterInvoiceController::class, 'destroy'])->name(
                        'masterInvoices.destroy',
                    );
                    Route::get('/{masterInvoice}/activate', [MasterInvoiceController::class, 'activate'])->name(
                        'masterInvoices.activate',
                    );
                    Route::patch('/{masterInvoice}/active', [MasterInvoiceController::class, 'active'])->name(
                        'masterInvoices.active',
                    );
                    Route::patch('/{masterInvoice}/pause', [MasterInvoiceController::class, 'pause'])->name(
                        'masterInvoices.pause',
                    );
                },
            );

            Route::group(['prefix' => 'masterLineItems'], function () {
                Route::post('/', [MasterLineItemController::class, 'store'])->name('masterLineItems.store');
                Route::get('/{masterInvoice}/create', [MasterLineItemController::class, 'create'])->name(
                    'masterLineItems.create',
                );
                Route::get('/{masterLineItem}/edit', [MasterLineItemController::class, 'edit'])->name(
                    'masterLineItems.edit',
                );
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

require_once __DIR__.'/jetstream.php';
