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
use Workdo\Procurement\Http\Controllers\BudgetTypeController;
use Workdo\Procurement\Http\Controllers\DashboardController;
use Workdo\Procurement\Http\Controllers\ProcurementCustomQuestionController;
use Workdo\Procurement\Http\Controllers\ProcurementInterviewScheduleController;
use Workdo\Procurement\Http\Controllers\RfxApplicantController;
use Workdo\Procurement\Http\Controllers\RfxApplicationController;
use Workdo\Procurement\Http\Controllers\RfxCategoryController;
use Workdo\Procurement\Http\Controllers\RfxController;
use Workdo\Procurement\Http\Controllers\RfxStageController;


Route::group(['middleware' => ['web', 'auth','verified','PlanModuleCheck:Procurement']], function () {

    Route::get('dashboard/procurement', [DashboardController::class, 'index'])->name('procurement.dashboard');
    Route::resource('rfx-stage', RfxStageController::class);
    Route::post('rfx-stage/order', [RfxStageController::class, 'order'])->name('rfx.stage.order');
    Route::post('rfx/product', [RfxController::class, 'product'])->name('rfx.product');
    Route::post('rfx-items', [RfxController::class, 'items'])->name('rfx.items');

    Route::resource('rfx', RfxController::class);
    Route::get('rfx-grid', [RfxController::class, 'grid'])->name('rfx.grid');
    Route::resource('rfx-category', RfxCategoryController::class);
    Route::resource('budgettype', BudgetTypeController::class);
    Route::resource('rfx-application', RfxApplicationController::class);
    Route::post('rfx-application/getRfx', [RfxApplicationController::class, 'getRfx'])->name('get.rfx.application');
    Route::post('rfx-application/order', [RfxApplicationController::class, 'order'])->name('rfx.application.order');
    Route::get('applicant-rfx-applications', [RfxApplicationController::class, 'archived'])->name('rfx.application.archived');

    Route::get('rfx-application-list', [RfxApplicationController::class, 'list'])->name('rfx.list');

    Route::post('rfx-application/{id}/rating', [RfxApplicationController::class, 'rating'])->name('rfx.application.rating');

    Route::delete('rfx-application/{id}/archive', [RfxApplicationController::class, 'archive'])->name('rfx.application.archive');

    Route::post('rfx-application/stage/change', [RfxApplicationController::class, 'stageChange'])->name('rfx.application.stage.change');

    Route::post('rfx-application/{id}/skill/store', [RfxApplicationController::class, 'addSkill'])->name('rfx.application.skill.store');

    Route::post('rfx-application/{id}/note/store', [RfxApplicationController::class, 'addNote'])->name('rfx.application.note.store');
    Route::delete('rfx-application/{id}/note/destroy', [RfxApplicationController::class, 'destroyNote'])->name('rfx.application.note.destroy');
    Route::get('vendor-onboard', [RfxApplicationController::class, 'vendorOnBoard'])->name('vendor.on.board');
    Route::get('vendor-onboard/create/{id}', [RfxApplicationController::class, 'vendorBoardCreate'])->name('vendor.on.board.create');
    Route::post('vendor-onboard/store/{id}', [RfxApplicationController::class, 'vendorBoardStore'])->name('vendor.on.board.store');
    Route::get('vendor-onboard/edit/{id}', [RfxApplicationController::class, 'vendorBoardEdit'])->name('vendor.on.board.edit');
    Route::post('vendor-onboard/update/{id}', [RfxApplicationController::class, 'vendorBoardUpdate'])->name('vendor.on.board.update');
    Route::delete('vendor-onboard/delete/{id}', [RfxApplicationController::class, 'vendorBoardDelete'])->name('vendor.on.board.delete');
    Route::get('vendor-onboard-grid', [RfxApplicationController::class, 'grid'])->name('vendor.on.board.grid');
    Route::get('vendor-onboard/convert/{id}', [RfxApplicationController::class, 'vendorBoardConvert'])->name('vendor.on.board.converts');
    Route::post('vendor-onboard/convert/{id}', [RfxApplicationController::class, 'vendorBoardConvertData'])->name('vendor.on.board.convert');
    Route::get('rfx-vendor', [RfxApplicationController::class, 'getRFxVendor'])->name('rfx.vendor');

    Route::resource('rfx-applicant', RfxApplicantController::class);

    Route::resource('rfx-custom-question', ProcurementCustomQuestionController::class);
    Route::resource('rfx-interview-schedule', ProcurementInterviewScheduleController::class);

});

Route::group(['middleware' => 'web'], function () {
    Route::get('rfx-list/{slug?}/{lang?}', [RfxController::class, 'rfxListing'])->name('rfx-list');
    Route::get('rfx/requirement/{code}/{lang}', [RfxController::class, 'rfxRequirement'])->name('rfx.requirement');
    Route::get('rfx/apply/{code}/{lang}', [RfxController::class, 'rfxApply'])->name('rfx.apply');
    Route::get('rfx/terms_and_condition/{code}/{lang}', [RfxController::class, 'TermsAndCondition'])->name('rfx.terms.and.conditions');
    Route::post('rfx/apply/data/{code}', [RfxController::class, 'rfxApplyData'])->name('rfx.apply.data');
    Route::get('rfx/interview-schedule/detail/{id}', [ProcurementInterviewScheduleController::class, 'interviewDetail'])->name('rfx.interview.schedule.detail');
});
