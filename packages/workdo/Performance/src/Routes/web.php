<?php

use Illuminate\Support\Facades\Route;
use Workdo\Hrm\Http\Controllers\EmployeeController;
use Workdo\Performance\Http\Controllers\AppraisalController;
use Workdo\Performance\Http\Controllers\CompetenciesController;
use Workdo\Performance\Http\Controllers\GoalTrackingController;
use Workdo\Performance\Http\Controllers\GoalTypeController;
use Workdo\Performance\Http\Controllers\IndicatorController;
use Workdo\Performance\Http\Controllers\PerformanceTypeController;

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

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Performance']], function () {
    Route::resource('goaltype', GoalTypeController::class);

    //GoalTracking
    Route::resource('goaltracking', GoalTrackingController::class);
    Route::get('goaltracking-grid', [GoalTrackingController::class, 'grid'])->name('goaltracking.grid');

    //performanceType
    Route::resource('performanceType', PerformanceTypeController::class);

    //competencies
    Route::resource('competencies', CompetenciesController::class);

    //indicator
    Route::resource('indicator', IndicatorController::class);
    Route::post('employee/json', [EmployeeController::class, 'json'])->name('employee.json');

    //appraisal
    Route::resource('appraisal', AppraisalController::class);
    Route::post('/appraisals', [AppraisalController::class, 'empByStar'])->name('empByStar');
    Route::post('/appraisals1', [AppraisalController::class, 'empByStar1'])->name('empByStar1');
    Route::post('/getemployee', [AppraisalController::class, 'getemployee'])->name('getemployee');
    Route::post('/check-branch-indicator', [AppraisalController::class, 'checkBranchIndicator'])->name('checkBranchIndicator');
});
