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
use Workdo\Goal\Http\Controllers\GoalController;


Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Goal']], function () {
    Route::resource('goal', GoalController::class);

     //Goal import
     Route::get('goal/import/export', [GoalController::class,'fileImportExport'])->name('goal.file.import');
     Route::post('goal/import', [GoalController::class,'fileImport'])->name('goal.import');
     Route::get('goal/import/modal', [GoalController::class,'fileImportModal'])->name('goal.import.modal');
     Route::post('goal/data/import/', [GoalController::class,'goalImportdata'])->name('goal.import.data');
});
