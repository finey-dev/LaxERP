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
use Workdo\SWOTAnalysisModel\Http\Controllers\SWOTAnalysisModelController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:SWOTAnalysisModel']], function () {
    Route::resource('swotanalysis-model', SWOTAnalysisModelController::class)->except(['create']);
    Route::prefix('swotanalysismodel')->group(function () {
        Route::get('/', [SWOTAnalysisModelController::class, 'index']);
        Route::get('/create/{id}', [SWOTAnalysisModelController::class, 'create'])->name('swotanalysis-model.create');
        Route::post('/order', [SWOTAnalysisModelController::class, 'order'])->name('swotanalysismodel.order');
        Route::post('/{id}/rating', [SWOTAnalysisModelController::class, 'rating'])->name('swotanalysismodel.rating');
        Route::get('/{id}/comment/{cid}/reply', [SWOTAnalysisModelController::class, 'swotanalysismodelCommentReply'])->name('swotanalysismodel.comment.reply');
        Route::post('/{id}/comment', [SWOTAnalysisModelController::class, 'swotanalysismodelCommentStore'])->name('swotanalysismodel.comment.store');
    });

    Route::get('swotanalysis-model-grid', [SWOTAnalysisModelController::class, 'grid'])->name('swotanalysis-model.grid');
    Route::get('swotanalysis-model-kanban', [SWOTAnalysisModelController::class, 'swotanalysismodel_kanban'])->name('swotanalysis-model.kanban');
    Route::get('swotanalysis-model-treeview', [SWOTAnalysisModelController::class, 'swotanalysismodel_treeview'])->name('swotanalysis-model.treeview');
    Route::post('swotanalysismodel-gettreeview', [SWOTAnalysisModelController::class, 'swotanalysismodel_getTreeView'])->name('swotanalysismodel.gettreeview');

    Route::match(['get', 'post'], 'swotanalysis-model-receipt/{receipt}', [SWOTAnalysisModelController::class, 'receipt'])->name('swotanalysis-model.receipt');

    Route::post('swotanalysis/{id}/strength', [SWOTAnalysisModelController::class, 'strengthStore'])->name('swotanalysis.strength.store');
    Route::post('swotanalysis/{id}/description', [SWOTAnalysisModelController::class, 'descriptionStore'])->name('swotanalysis-model.description.store');
    Route::post('swotanalysis/{id}/weakness', [SWOTAnalysisModelController::class, 'WeaknessDescStore'])->name('swotanalysis-model.weakness.store');
    Route::post('swotanalysis/{id}/notes', [SWOTAnalysisModelController::class, 'NotesDescStore'])->name('swotanalysis-model.notes.store');
    Route::post('swotanalysis/{id}/opportunities', [SWOTAnalysisModelController::class, 'OpportunitiesDescStore'])->name('swotanalysis-model.opportunities.store');
    Route::post('swotanalysis/{id}/threats', [SWOTAnalysisModelController::class, 'ThreatsDescStore'])->name('swotanalysis-model.threats.store');
});
