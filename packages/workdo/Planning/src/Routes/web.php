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
use Workdo\Planning\Http\Controllers\PlanningCetegoriesController;
use Workdo\Planning\Http\Controllers\PlanningChallengeController;
use Workdo\Planning\Http\Controllers\PlanningChartersController;
use Workdo\Planning\Http\Controllers\PlanningStageController;
use Workdo\Planning\Http\Controllers\PlanningStatusController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Planning']], function () {

    Route::resource('planningchallenges', PlanningChallengeController::class);
    Route::resource('planningcharters', PlanningChartersController::class);
    Route::resource('planning-categories', PlanningCetegoriesController::class);
    Route::resource('planning-stage', PlanningStageController::class);
    Route::resource('planning-status', PlanningStatusController::class);
    Route::get('charter-grid', [PlanningChartersController::class, 'grid'])->name('charters.grid');
    Route::get('charters/create/{id}', [PlanningChartersController::class, 'create'])->name('charters.create');
    Route::post('charters/{id}/rating', [PlanningChartersController::class, 'rating'])->name('charters.rating');
    Route::post('charters/{id}/comment', [PlanningChartersController::class, 'chartersCommentStore'])->name('charters.comment.store');
    Route::get('charters/{id}/comment/{cid}/reply', [PlanningChartersController::class, 'chartersCommentReply'])->name('charters.comment.reply');

    Route::get('charters-kanban', [PlanningChartersController::class, 'charters_kanban'])->name('charters.kanban');

    Route::post('charters/order', [PlanningChartersController::class, 'order'])->name('charters.order');

    Route::get('charters/create/{id}', [PlanningChartersController::class, 'create'])->name('charters.create');

    Route::get('charters-treeview', [PlanningChartersController::class, 'charters_treeview'])->name('charters.treeview');
    Route::post('charters-gettreeview', [PlanningChartersController::class, 'charters_getTreeView'])->name('charters.gettreeview');
    Route::post('charters/{id}/organisational', [PlanningChartersController::class, 'organisationalStore'])->name('charters.organisational.store');

    Route::post('charters/{id}/description', [PlanningChartersController::class, 'descriptionStore'])->name('charters.description.store');
    Route::post('charters/{id}/goal', [PlanningChartersController::class, 'GoalDescStore'])->name('charters.goal.store');
    Route::post('charters/{id}/notes', [PlanningChartersController::class, 'NotesDescStore'])->name('charters.notes.store');

    Route::match(['get', 'post'], '/charter-receipt/{receipt}', [PlanningChartersController::class, 'receipt'])->name('charters.receipt');
});
