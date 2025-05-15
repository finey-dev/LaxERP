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
use Workdo\RecurringInvoiceBill\Http\Controllers\Company\SettingsController;

Route::group(['middleware' => ['web','verified','auth','PlanModuleCheck:RecurringInvoiceBill']], function ()
{
    Route::post('/setting/recurring_store', [SettingsController::class,'store'])->name('recurring.setting.store');

});
