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
use Workdo\TeamWorkload\Http\Controllers\HolidaysController;
use Workdo\TeamWorkload\Http\Controllers\StaffSettingsController;
use Workdo\TeamWorkload\Http\Controllers\WorkloadTimesheetController;
use Workdo\TeamWorkload\Http\Controllers\TeamWorkloadController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:TeamWorkload']], function () {

    Route::resource('workload', TeamWorkloadController::class);
    Route::resource('staff-setting', StaffSettingsController::class);
    Route::resource('holidays', HolidaysController::class);
    Route::resource('workload-timesheet', WorkloadTimesheetController::class);

    Route::get('/workload-totalhours',[WorkloadTimesheetController::class, 'totalhours'])->name('workload-timesheet.totalhours');
    Route::post('workload-update', [StaffSettingsController::class, 'workloadstore'])->name('staff-setting.workloadstore');

    Route::get('teamworkload-holidays/calender', [HolidaysController::class, 'calenders'])->name('holidays.calender');
});
