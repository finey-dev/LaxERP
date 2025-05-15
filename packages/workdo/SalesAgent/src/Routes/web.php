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
use Workdo\SalesAgent\Http\Controllers\ProgramController;
use Workdo\SalesAgent\Http\Controllers\SalesAgentController;
use Workdo\SalesAgent\Http\Controllers\SalesAgentPurchaseController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:SalesAgent']], function () {
    Route::prefix('salesagent')->group(function () {

        Route::resource('/user', SalesAgentController::class)->names('salesagents');

        Route::get('dashboard/', [SalesAgentController::class, 'dashboard'])->name('salesagent.dashboard');

        Route::get('/', [SalesAgentController::class, 'index'])->name('management.index');

        Route::any('settings', [SalesAgentController::class, 'setting'])->name('salesagents.setting.save');

        Route::post('sales-agent-status', [SalesAgentController::class, 'changeSalesAgentStatus'])->name('activeSalesAgent');

        // programs
        Route::resource('programs', ProgramController::class)->names('programs');

        Route::get('program/join-requests/{id}', [ProgramController::class, 'requestList'])->name('salesagent.program.request.list');

        Route::get('program/send-request/{programId}/{id?}', [ProgramController::class, 'sendRequest'])->name('salesagent.program.send.request');

        Route::get('program/accept-request/{programId}/{id?}', [ProgramController::class, 'acceptRequest'])->name('salesagent.program.accept.request');

        Route::any('program/reject-request/{programId}/{id?}', [ProgramController::class, 'rejectRequest'])->name('salesagent.program.reject.request');

        // purchase
        Route::get('purchase/order', [SalesAgentPurchaseController::class, 'index'])->name('salesagent.purchase.order.index');

        Route::get('purchase/create/', [SalesAgentPurchaseController::class, 'create'])->name('salesagents.purchase.order.create');

        Route::post('purchase/store/', [SalesAgentPurchaseController::class, 'store'])->name('salesagents.purchase.order.store');

        Route::get('purchase/edit/{id}', [SalesAgentPurchaseController::class, 'edit'])->name('salesagents.purchase.order.edit');

        Route::get('purchase/show/{id}', [SalesAgentPurchaseController::class, 'show'])->name('salesagents.purchase.order.show');

        Route::post('purchase/update/{id}', [SalesAgentPurchaseController::class, 'update'])->name('salesagents.purchase.order.update');

        Route::any('purchase/delete/{id}', [SalesAgentPurchaseController::class, 'destroy'])->name('salesagents.purchase.order.destroy');

        Route::any('purchase/invoice', [SalesAgentPurchaseController::class, 'invoiceIndex'])->name('salesagent.purchase.invoices.index');

        Route::get('purchase/invoice-details/{id}', [SalesAgentPurchaseController::class, 'invoiceShow'])->name('salesagent.purchase.invoice.show');

        Route::get('purchase/invoice/{id}', [SalesAgentPurchaseController::class, 'invoiceCreate'])->name('salesagents.purchase.invoice.model');

        Route::get('purchase/order/update/{order_id}/{key}', [SalesAgentPurchaseController::class, 'updateOrderStatus'])->name('salesagents.update.purchase.order.status');

        Route::any('purchase/get-program-items', [SalesAgentPurchaseController::class, 'getProgramItems'])->name('get.program.items');

        Route::post('product', [SalesAgentPurchaseController::class, 'product'])->name('salesagent.program.product');

        Route::post('purchase/setting/store', [SalesAgentPurchaseController::class, 'settings'])->name('salesagent.purchase.setting');

        Route::get('purchase/setting/create', [SalesAgentPurchaseController::class, 'settingsCreate'])->name('salesagent.purchase.setting.create');

        Route::get('product/list/{id?}', [SalesAgentPurchaseController::class, 'productList'])->name('salesagent.product.list');

        Route::get('customers', [SalesAgentPurchaseController::class, 'setting'])->name('salesagent.customers.index');
    });
});
