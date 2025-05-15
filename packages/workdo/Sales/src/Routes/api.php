<?php

use Illuminate\Http\Request;
use Workdo\Sales\Http\Controllers\Api\DashboardController;
use Workdo\Sales\Http\Controllers\Api\MeetingsController;
use Workdo\Sales\Http\Controllers\Api\OpportunitiesController;
use Workdo\Sales\Http\Controllers\Api\QuotesController;
use Workdo\Sales\Http\Controllers\Api\SalesOrderController;

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

Route::middleware('auth:api')->get('/sales', function (Request $request) {
    return $request->user();
});

Route::prefix('Sales')->group(function () {
    Route::middleware(['jwt.api.auth'])->group(function () {
        Route::get('opportunities',[OpportunitiesController::class,'index']);
        Route::get('RequestData',[OpportunitiesController::class,'create']);
        Route::post('opportunitity/store',[OpportunitiesController::class,'store']);
        Route::post('opportunitity/update/{id}',[OpportunitiesController::class,'update']);
        Route::post('opportunitity/delete/{id}',[OpportunitiesController::class,'destroy']);

        Route::get('quotes',[QuotesController::class,'index']);
        Route::post('quote/create',[QuotesController::class,'create']);
        Route::post('quote/update/{id}',[QuotesController::class,'update']);
        Route::post('quote/delete/{id}',[QuotesController::class,'destroy']);

        Route::get('sales-orders',[SalesOrderController::class,'index']);
        Route::post('sales-order/create',[SalesOrderController::class,'store']);
        Route::post('sales-order/update/{id}',[SalesOrderController::class,'update']);
        Route::post('sales-order/delete/{id}',[SalesOrderController::class,'destroy']);

        Route::get('sales/home',[DashboardController::class,'index']);

        Route::get('meeetings',[MeetingsController::class,'index']);
        Route::post('meeting/store',[MeetingsController::class,'store']);
        Route::post('meeting/update/{id}',[MeetingsController::class,'update']);
        Route::post('meeting/destroy/{id}', [MeetingsController::class, 'destroy']);
        Route::get('meeting/create',[MeetingsController::class,'create']);
    });
});
