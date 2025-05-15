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
use Workdo\RepairManagementSystem\Http\Controllers\RepairInvoiceController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairMovementHistoryController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairOrderRequestController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairProductPartController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairTechnicianController;
use Workdo\RepairManagementSystem\Http\Controllers\RepairWarrantyController;

Route::group(['middleware' => ['web','auth','verified','PlanModuleCheck:RepairManagementSystem']], function ()
{
    //repair order request
    Route::get('repair-order-request', [RepairOrderRequestController::class,'index'])->name('repair.request.index');
    Route::get('repair-order-request/create', [RepairOrderRequestController::class,'create'])->name('repair.request.create');
    Route::post('repair-order-request/store', [RepairOrderRequestController::class,'store'])->name('repair.request.store');
    Route::get('repair-order-request/edit/{id}', [RepairOrderRequestController::class,'edit'])->name('repair.request.edit');
    Route::put('repair-order-request/update/{id}', [RepairOrderRequestController::class,'update'])->name('repair.request.update');
    Route::delete('repair-order-request/delete/{id}', [RepairOrderRequestController::class,'destroy'])->name('repair.request.destroy');
    Route::get('repair-order-request-steps-changes/{id}/{response}', [RepairOrderRequestController::class, 'repairOrderStepsChange'])->name('repair.request.status.steps.change');

    //movement history
    Route::get('repair-movement-history/{id}', [RepairMovementHistoryController::class,'index'])->name('repair.movement.hostory.index');

    Route::get('repair-parts/create/{id}', [RepairProductPartController::class,'create'])->name('repair.parts.create');
    Route::post('repair-parts/product', [RepairProductPartController::class, 'product'])->name('repair.parts.product');
    Route::post('repair-parts/store', [RepairProductPartController::class,'store'])->name('repair.parts.store');
    Route::get('repair-parts/edit/{id}/{type?}', [RepairProductPartController::class,'edit'])->name('repair.parts.edit');
    Route::put('repair-parts/update/{id}', [RepairProductPartController::class,'update'])->name('repair.parts.update');
    Route::get('repair-parts/parts', [RepairProductPartController::class, 'parts'])->name('repair.parts.parts');
    Route::post('repair-parts/destroy', [RepairProductPartController::class, 'partDestroy'])->name('repair.parts.destroy');

    Route::get('repair-invoice', [RepairInvoiceController::class,'index'])->name('repair.request.invoice.index');
    Route::get('repair-invoice/create/{id}', [RepairInvoiceController::class,'create'])->name('repair.request.invoice');
    Route::post('repair-invoice/store/{id}', [RepairInvoiceController::class,'store'])->name('repair.request.invoice.store');
    Route::get('repair-invoice/show/{id}', [RepairInvoiceController::class,'show'])->name('repair.request.invoice.show');

    Route::get('repair-invoice/payment/create/{id}', [RepairInvoiceController::class,'payment'])->name('repair.invoice.payment.create');
    Route::post('repair-invoice/payment/store/{id}', [RepairInvoiceController::class,'createPayment'])->name('repair.invoice.payment.store');

    Route::post('repair/setting/store', [RepairInvoiceController::class,'setting'])->name('repair.setting.store');
    Route::resource('repair-technician', RepairTechnicianController::class);
    Route::resource('repair-warranty', RepairWarrantyController::class);
    Route::get('/get-repair-parts', [RepairWarrantyController::class, 'getRepairParts'])->name('get-repair-parts');
    Route::get('repair-warranty/{id}/terms', [RepairWarrantyController::class, 'viewcontent'])->name('repair-warranty.terms');


});
