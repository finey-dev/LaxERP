<?php

use Illuminate\Support\Facades\Route;
use Workdo\Training\Http\Controllers\TrainerController;
use Workdo\Training\Http\Controllers\TrainingController;
use Workdo\Training\Http\Controllers\TrainingTypeController;

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

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Training']], function () {

    Route::post('training/status', [TrainingController::class, 'updateStatus'])->name('training.status');
    Route::resource('training', TrainingController::class);

    //Trainer
    Route::resource('trainer', TrainerController::class);

    //Trainingtype
    Route::resource('trainingtype', TrainingTypeController::class);
});
