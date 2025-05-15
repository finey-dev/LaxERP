<?php

use Illuminate\Support\Facades\Route;
use Workdo\BiometricAttendance\Http\Controllers\BiometricAttendanceController;

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
Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:BiometricAttendance']], function () {

    Route::prefix('biometricattendance')->group(function () {

        Route::resource('/biometric-attendance', BiometricAttendanceController::class);

        Route::post('/biometric-attendance/sync/{start_date?}/{end_date?}', [BiometricAttendanceController::class, 'AllSync'])->name('biometric-attendance.allsync');

        Route::get('/biometric-settings', [BiometricAttendanceController::class, 'SettingCreate'])
            ->name('biometric-settings.index');

        Route::post('/biometric-setting/store', [BiometricAttendanceController::class, 'SettingStore'])->name('biometric-settings.store');
    });
});
