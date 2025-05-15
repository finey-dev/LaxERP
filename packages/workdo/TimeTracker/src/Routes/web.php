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
use Workdo\TimeTracker\Http\Controllers\TimeTrackerController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:TimeTracker']], function () {
    Route::resource('timetracker', TimeTrackerController::class);

    Route::post('/image-view', [TimeTrackerController::class, 'getTrackerImages'])->name('tracker.image.view');
    Route::delete('/image-remove', [TimeTrackerController::class, 'removeTrackerImages'])->name('tracker.image.remove');
    Route::delete('tracker/{tid}/destroy', [TimeTrackerController::class, 'Destroy'])->name('tracker.destroy');
    Route::post('setting-store', [TimeTrackerController::class, 'setting'])->name('timetracker.setting.store');
});
