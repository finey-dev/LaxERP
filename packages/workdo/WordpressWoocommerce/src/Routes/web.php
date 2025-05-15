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
use Workdo\WordpressWoocommerce\Http\Controllers\WordpressWoocommerceController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpCategoryController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpCouponController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpCustomerController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpOrderController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpProductController;
use Workdo\WordpressWoocommerce\Http\Controllers\WpTaxController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:WordpressWoocommerce']], function () {
    Route::post('woocommerce/setting', [WordpressWoocommerceController::class,'setting'])->name('wordpress.setting');
    Route::resource('wp-customer',WpCustomerController::class);
    Route::resource('wp-product',WpProductController::class);
    Route::resource('wp-order',WpOrderController::class);
    Route::resource('wp-category',WpCategoryController::class);
    Route::resource('wp-coupon',WpCouponController::class);
    Route::resource('wp-tax',WpTaxController::class);
});
