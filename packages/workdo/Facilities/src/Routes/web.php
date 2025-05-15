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
use Workdo\ProductService\Http\Controllers\ProductServiceController;
use Workdo\Facilities\Http\Controllers\FacilitiesServiceController;
use Workdo\Facilities\Http\Controllers\FacilitiesSpaceController;
use Workdo\Facilities\Http\Controllers\FacilitiesWorkingController;
use Workdo\Facilities\Http\Controllers\FacilitiesBookingController;
use Workdo\Facilities\Http\Controllers\FacilitiesBookingOrderController;
use Workdo\Facilities\Http\Controllers\FacilitiesController;
use Workdo\Facilities\Http\Controllers\FacilitiesForntBookingController;

Route::group(['middleware' => ['web','auth','verified','PlanModuleCheck:Facilities']], function () {
    Route::prefix('facility')->group(function() {

        Route::get('facilities',[FacilitiesController::class,'index'])->name('facilities.dashboard');

        Route::resource('facility-booking', FacilitiesBookingController::class);
        Route::resource('facilities-service',FacilitiesServiceController::class);
        Route::resource('facilities-space',FacilitiesSpaceController::class);
        Route::resource('facilities-working',FacilitiesWorkingController::class);
        Route::get('services', [ProductServiceController::class,'create'])->name('service');
        Route::resource('facility-booking-order', FacilitiesBookingOrderController::class);
        Route::Post('facility-booking/stage/order',[FacilitiesBookingOrderController::class,'stageOrder'])->name('facilities.booking.stage.order');
        Route::get('booking-receipt',[FacilitiesBookingOrderController::class,'receipt'])->name('facilities.booking.receipt');
        Route::get('booking-receipt-show/{id}',[FacilitiesBookingOrderController::class,'receiptShow'])->name('booking.receipt.show');

        Route::any('/search-booking', [FacilitiesBookingController::class,'searchBooking'])->name('search.facilities.booking');
        Route::any('/users-detail', [FacilitiesBookingController::class,'usersDetail'])->name('users.detail');
        Route::any('/users', [FacilitiesBookingController::class,'users'])->name('users');

    });
});
Route::group(['middleware' => 'web'], function () {

    Route::get('/facility-booking/{slug}/{lang?}', [FacilitiesForntBookingController::class,'index'])->name('facilities.booking');
    Route::any('/search-facility-booking/{id}/{slug}', [FacilitiesForntBookingController::class,'searchFacilitiesBooking'])->name('facilities.search');
    Route::post('/facility-booking-store/{slug}', [FacilitiesForntBookingController::class,'store'])->name('facilities.store');
    Route::get('{slug}/facilities/{lang?}', [FacilitiesForntBookingController::class, 'ShowFrontPage'])->name('facilities.home');

});
