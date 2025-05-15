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
use Workdo\BusinessProcessMapping\Http\Controllers\BusinessProcessMappingController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:BusinessProcessMapping']], function () {
    Route::prefix('businessprocessmapping')->group(function () {
        Route::resource('business-process-mapping', BusinessProcessMappingController::class);
        Route::post('mapping/relateds/get',[BusinessProcessMappingController::class ,'relatedGet'])->name('mapping.relateds.get');
        Route::any('mapping/relateds/update',[BusinessProcessMappingController::class ,'relatedUpdate'])->name('mapping.relateds.update');
        Route::get('flowchart',[BusinessProcessMappingController::class ,'flowchart'])->name('flowchart');
        Route::get('api/store-flow-chart/{id}',[BusinessProcessMappingController::class ,'storeFlowChart'])->name('store.flowchart');
        Route::post('api/get-flow-chart',[BusinessProcessMappingController::class ,'getFlowChart'])->name('get.flowchart');
        Route::get('business/preview/{id}',[BusinessProcessMappingController::class ,'flowchartPreview'])->name('business.preview');
        Route::any('sendmail/{id}',[BusinessProcessMappingController::class ,'sendMail'])->name('send.business.mail');
        Route::post('flowchart/mail',[BusinessProcessMappingController::class ,'sendMailFlowchart'])->name('mail.flowchart');
        Route::get('mapping/index/{related_id}/{id}',[BusinessProcessMappingController::class ,'mappingIndex'])->name('mapping.index');
    });
});

Route::group(['middleware' => ['web']], function () {
    Route::get('business/shared/link/{id}',[BusinessProcessMappingController::class ,'bussinessMapSharedLink'])->name('business.shared.link');
});
