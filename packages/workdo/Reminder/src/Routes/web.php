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
use Workdo\Reminder\Http\Controllers\ReminderController;
use Workdo\Reminder\Http\Controllers\Company\SettingsController;

Route::middleware(['web','auth','verified','PlanModuleCheck:Reminder'])->group(function (){
    Route::resource('reminder', ReminderController::class);
    Route::post('reminder-module',[ ReminderController::class ,'module_data'])->name('get.moduledata');
    Route::post('reminder-attribute',[ ReminderController::class ,'reminder_attribute'])->name('reminder.attribute');
    Route::post('reminder-attribute-edit/{id}',[ ReminderController::class ,'reminder_attribute_edit'])->name('reminder.attribute.edit');
    Route::post('deal-client',[ ReminderController::class ,'deal_client'])->name('deal.client');
    Route::post('reminder-setting',[ SettingsController::class ,'store'])->name('reminder.setting.store');
});
