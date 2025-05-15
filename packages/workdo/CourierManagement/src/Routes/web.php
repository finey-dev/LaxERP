<?php

use Illuminate\Support\Facades\Route;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Workdo\CourierManagement\Http\Controllers\BranchController;
use Workdo\CourierManagement\Http\Controllers\CourierAgentsController;
use Workdo\CourierManagement\Http\Controllers\CourierContractsController;
use Workdo\CourierManagement\Http\Controllers\ServicetypeController;
use Workdo\CourierManagement\Http\Controllers\TrackingstatusController;
use Workdo\CourierManagement\Http\Controllers\PackageCategoryController;
use Workdo\CourierManagement\Http\Controllers\CourierManagementController;
use Workdo\CourierManagement\Http\Controllers\ManualPaymentController;
use Workdo\CourierManagement\Http\Controllers\PaymentInfoController;
use Workdo\CourierManagement\Http\Controllers\CourierManagementDashboardController;
use Workdo\CourierManagement\Http\Controllers\CourierReturnsController;
use Workdo\CourierManagement\Http\Controllers\ServiceAgreementsController;

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
// Dashboard Route
Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:CourierManagement']], function () {

    Route::get('couriermanagement', [CourierManagementDashboardController::class, 'index'])->name('dashboard.courier.management');
    Route::prefix('courier-management')->group(function () {
        // For Courier
        Route::get('courier', [CourierManagementController::class, 'index'])->name('courier');
        Route::get('courier/create', [CourierManagementController::class, 'create'])->name('courier.create');
        Route::post('courier/store', [CourierManagementController::class, 'store'])->name('courier.store');
        Route::get('courier/edit/{trackingId}', [CourierManagementController::class, 'edit'])->name('courier.edit');
        Route::post('courier/update/{trackingId}', [CourierManagementController::class, 'update'])->name('courier.update');
        Route::delete('courier/delete/{trackingId}', [CourierManagementController::class, 'destroy'])->name('courier.delete');
        Route::get('courier/show/{trackingId}', [CourierManagementController::class, 'show'])->name('courier.show');
        Route::post('courier/destination/', [CourierManagementController::class, 'getBranch'])->name('courier.get.branch');

        //For Courier Payment
        Route::get('courier/payment/{trackingId}', [ManualPaymentController::class, 'courierPayment'])->name('courier.paymnent');
        Route::post('courier/makepayment/{trackingId}/{courierPackageId}', [ManualPaymentController::class, 'makePayment'])->name('make.paymnent');
        Route::get('courier/edit/paymentdetails/{trackingId}', [ManualPaymentController::class, 'edit'])->name('edit.paymentdetails');
        Route::post('courier/update/paymentdetails/{trackingId}', [ManualPaymentController::class, 'update'])->name('update.paymentdetails');

        // For Pending courier Request
        Route::get('pending-courier-request', [CourierManagementController::class, 'getPendingCourierRequest'])->name('get.pending.courier.request');
        Route::get('approve-courier-request/{trackingId}', [CourierManagementController::class, 'approveCourierRequest'])->name('approve.courier.request');
        Route::get('reject-courier-request/{trackingId}', [CourierManagementController::class, 'rejectCourierRequest'])->name('reject.courier.request');
        Route::delete('delete-courier-pending-request/{trackingId}', [CourierManagementController::class, 'deletePendingCourierRequest'])->name('delete.courier.pending.request');
        Route::get('show-courier-request/{trackingId}', [CourierManagementController::class, 'showCourierPendingRequest'])->name('show.courier.pending.request');

        // Payment Details page Routes
        Route::get('courier/paymentdetails/', [PaymentInfoController::class, 'index'])->name('courier.all.payment');
        Route::delete('courier/delete/paymentdetails/{trackingId}', [PaymentInfoController::class, 'destroy'])->name('delete.paymentdetails');

        //Get Payment Data
        Route::get('courier/findPaymentInfo', [PaymentInfoController::class, 'index'])->name('payment.info');



        // For Branch
        Route::get('branch', [BranchController::class, 'index'])->name('courier.branch');
        Route::get('branch/create', [BranchController::class, 'create'])->name('courier.branch.create');
        Route::post('branch/store', [BranchController::class, 'store'])->name('courier.branch.store');
        Route::get('branch/edit/{branchId}', [BranchController::class, 'edit'])->name('courier.branch.edit');
        Route::post('branch/update/{branchId}', [BranchController::class, 'update'])->name('courier.branch.update');
        Route::delete('branch/delete/{branchId}', [BranchController::class, 'destroy'])->name('courier.branch.delete');
        Route::get('branch/show/{branchId}', [BranchController::class, 'show'])->name('courier.branch.show');


        // For Service Type
        Route::get('servicetype', [ServicetypeController::class, 'index'])->name('courier.servicetype');
        Route::get('servicetype/create', [ServicetypeController::class, 'create'])->name('courier.servicetype.create');
        Route::post('servicetype/store', [ServicetypeController::class, 'store'])->name('courier.servicetype.store');
        Route::get('servicetype/edit/{servicetypeId}', [ServicetypeController::class, 'edit'])->name('courier.servicetype.edit');
        Route::post('servicetype/update/{servicetypeId}', [ServicetypeController::class, 'update'])->name('courier.servicetype.update');
        Route::delete('servicetype/delete/{servicetypeId}', [ServicetypeController::class, 'destroy'])->name('courier.servicetype.delete');

        // For Tracking Status
        Route::get('tracking/status', [TrackingstatusController::class, 'index'])->name('courier.tracking.status');
        Route::get('tracking/status/create', [TrackingstatusController::class, 'create'])->name('courier.tracking.status.create');
        Route::post('tracking/status/store', [TrackingstatusController::class, 'store'])->name('courier.tracking.status.store');
        Route::get('tracking/status/edit/{trackingStatusId}', [TrackingstatusController::class, 'edit'])->name('courier.tracking.status.edit');
        Route::post('tracking/status/update/{trackingStatusId}', [TrackingstatusController::class, 'update'])->name('courier.tracking.status.update');
        Route::delete('tracking/status/delete/{trackingStatusId}', [TrackingstatusController::class, 'destroy'])->name('courier.tracking.status.delete');
        Route::post('tracking/status/order', [TrackingstatusController::class, 'orderUpdate'])->name('tracking.status.order');


        // For Package Category
        Route::get('package/category/status', [PackageCategoryController::class, 'index'])->name('courier.packagecategory.status');
        Route::get('package/category/create', [PackageCategoryController::class, 'create'])->name('courier.packagecategory.create');
        Route::post('package/category/store', [PackageCategoryController::class, 'store'])->name('courier.packagecategory.store');
        Route::get('package/category/edit/{categoryId}', [PackageCategoryController::class, 'edit'])->name('courier.packagecategory.edit');
        Route::post('package/category/update/{categoryId}', [PackageCategoryController::class, 'update'])->name('courier.packagecategory.update');
        Route::delete('package/category/delete/{categoryId}', [PackageCategoryController::class, 'destroy'])->name('courier.packagecategory.delete');

        // save courier setting
        Route::post('courier-settings-save', [CourierManagementController::class, 'courierSettingsStore'])->name('courier.setting.store');

        Route::post('trackingstatus/update/{trackingId}', [CourierManagementController::class, 'updateTrackingStatus'])->name('update.trackingstatus');

        Route::resource('courier-agents', CourierAgentsController::class);
        Route::resource('service-agreements', ServiceAgreementsController::class);
        Route::resource('courier-contracts', CourierContractsController::class);
        Route::resource('courier-returns', CourierReturnsController::class);


    });
});

Route::group(['middleware' => 'web'], function () {
    Route::post('get-destination-branch/{workspaceId}', [CourierManagementController::class, 'getDestinationBranch'])->name('courier.get.branch.publicrequest');

    Route::get('create-public-courier/{workspaceSlug}', [CourierManagementController::class, 'createPublicCourierRequest'])->name('create.public.courier.request');
    Route::post('store-public-courier-request', [CourierManagementController::class, 'storePublicCourierRequestData'])->name('store.public.courier.request');

    Route::get('findcourier/{workspaceSlug}', [CourierManagementController::class, 'findCourier'])->name('find.courier');
    Route::post('trackcourier/{workspaceSlug}', [CourierManagementController::class, 'trackCourier'])->name('track.courier');
});
