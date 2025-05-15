<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Workdo\TimeTracker\Http\Controllers\Api\TrackerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api'])->group(function () {
    Route::post('tracker-login', [TrackerController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::get('/get-projects', [TrackerController::class, 'getProjects']);
        Route::get('/get-workspace', [TrackerController::class, 'getworkspace']);
        Route::post('/add-tracker', [TrackerController::class, 'addTracker']);
        Route::post('/stop-tracker', [TrackerController::class, 'addTracker']);
        Route::post('/upload-photos', [TrackerController::class, 'uploadImage']);
        Route::post('logout', [TrackerController::class, 'logout']);

    });
});