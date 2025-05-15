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
use Workdo\MarketingPlan\Http\Controllers\MarketingPlanController;
use Workdo\MarketingPlan\Http\Controllers\MarketingPlanItemController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:MarketingPlan']], function () {

    Route::resource('marketing-plan', MarketingPlanController::class);

    Route::match(['get', 'post'], '/marketing-plan-receipt/{receipt}', [MarketingPlanController::class,'receipt'])->name('marketing-plan.receipt');

    Route::post('marketing-plan/{id}/description', [MarketingPlanController::class,'descriptionStore'])->name('marketing-plan.description.store');
    Route::post('marketing-plan/{id}/businesssummary', [MarketingPlanController::class,'businesssummaryStore'])->name('marketing-plan.businesssummary.store');
    Route::post('marketing-plan/{id}/companydescription', [MarketingPlanController::class,'companydescriptionStore'])->name('marketing-plan.companydescription.store');
    Route::post('marketing-plan/{id}/team', [MarketingPlanController::class,'teamStore'])->name('marketing-plan.team.store');
    Route::post('marketing-plan/{id}/businessinitiative', [MarketingPlanController::class,'businessinitiativeStore'])->name('marketing-plan.businessinitiative.store');
    Route::post('marketing-plan/{id}/targetmarket', [MarketingPlanController::class,'targetmarketStore'])->name('marketing-plan.targetmarket.store');
    Route::post('marketing-plan/{id}/marketingchannels', [MarketingPlanController::class,'marketingchannelsStore'])->name('marketing-plan.marketingchannels.store');
    Route::post('marketing-plan/{id}/budget', [MarketingPlanController::class,'budgetStore'])->name('marketing-plan.budget.store');
    Route::post('marketing-plan/{id}/notes', [MarketingPlanController::class,'NotesDescStore'])->name('marketing-plan.notes.store');
    Route::post('marketing-plan/{id}/rating', [MarketingPlanController::class,'rating'])->name('marketing-plan.rating');
    Route::post('marketing-plan/{id}/comment', [MarketingPlanController::class,'marketingplanCommentStore'])->name('marketing-plan.comment.store');

    Route::get('marketing-plan/{id}/comment/{cid}/reply', [MarketingPlanController::class,'marketingplanCommentReply'])->name('marketing-plan.comment.reply');

    Route::get('marketing-plan-kanban', [MarketingPlanController::class,'marketingplan_kanban'])->name('marketing-plan.kanban');
    Route::post('marketing-plan-order', [MarketingPlanController::class,'order'])->name('marketing-plan.order');
    Route::get('marketing-plan-grid', [MarketingPlanController::class,'grid'])->name('marketing-plan.grid');
    Route::get('marketing-plan-treeview', [MarketingPlanController::class,'marketingplan_treeview'])->name('marketing-plan.treeview');
    Route::post('marketing-plan-gettreeview', [MarketingPlanController::class,'marketingplan_getTreeView'])->name('marketing-plan.gettreeview');

    Route::get('marketing-plan/item/create/{id}', [MarketingPlanItemController::class,'create'])->name('marketing-plan-item.create');
    Route::post('marketing-plan/item/store/{id}', [MarketingPlanItemController::class,'store'])->name('marketing-plan-item.store');
    Route::delete('marketing-plan/item/destroy/{id}', [MarketingPlanItemController::class,'destroy'])->name('marketing-plan-item.destroy');
    Route::get('marketing-plan-item/items/', [MarketingPlanItemController::class,'getItems'])->name('marketing-plan-item.items');
});
