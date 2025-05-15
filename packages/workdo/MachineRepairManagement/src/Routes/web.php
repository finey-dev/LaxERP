<?php

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

use Illuminate\Support\Facades\Route;
use Workdo\MachineRepairManagement\Http\Controllers\MachineRepairManagementController;
use Workdo\MachineRepairManagement\Http\Controllers\MachinesController;
use Workdo\MachineRepairManagement\Http\Controllers\MachineServiceAgreementController;
use Workdo\MachineRepairManagement\Http\Controllers\MachinesInvoiceController;
use Workdo\MachineRepairManagement\Http\Controllers\MachinesRepairHistroyController;
use Workdo\MachineRepairManagement\Http\Controllers\MachinesRepairRequestController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:MachineRepairManagement']], function () {

    // dashboard
    Route::get('dashboard/machine-repair', [MachineRepairManagementController::class, 'index'])->name('dashboard.machine.repair');

    // machine
    Route::resource('machine-repair', MachinesController::class);

    // machine repair request
    Route::resource('machine-repair-request', MachinesRepairRequestController::class);

    Route::post('machine-repair-invoice/request', [MachinesInvoiceController::class, 'request'])->name('machine.invoice.request');
    Route::post('machine-repair-invoice/product', [MachinesInvoiceController::class, 'product'])->name('machine.invoice.product');
    Route::any('items-machine', [MachinesInvoiceController::class, 'items'])->name('machine.items');
    Route::post('machine-repair-invoice/product/destroy', [MachinesInvoiceController::class, 'productDestroy'])->name('machine.invoice.product.destroy');
    Route::get('machine-repair-invoice/create/{rid}', [MachinesInvoiceController::class, 'create'])->name('machine.invoice.create');
    Route::get('machine-repair-invoice/pdf/{id}', [MachinesInvoiceController::class, 'invoicePdf'])->name('machine.invoice.pdf');
    Route::get('machine-repair-invoice/{id}/payment', [MachinesInvoiceController::class, 'payment'])->name('machine.invoice.payment');
    Route::post('machine-repair-invoice/{id}/payment/store', [MachinesInvoiceController::class, 'createPayment'])->name('machine.invoice.payment.store');
    Route::post('machine-repair-invoice/{id}/payment/{pid}/', [MachinesInvoiceController::class, 'paymentDestroy'])->name('machine.invoice.payment.destroy');
    Route::post('machine-repair-invoice/section/type', [MachinesInvoiceController::class, 'InvoiceSectionGet'])->name('machine.invoice.section.type');

    Route::resource('machine-repair-history', MachinesRepairHistroyController::class);
    Route::resource('machine-service-agreement', MachineServiceAgreementController::class);

});
