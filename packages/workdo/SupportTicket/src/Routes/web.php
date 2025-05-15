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
use Workdo\SupportTicket\Http\Controllers\ConversionController;
use Workdo\SupportTicket\Http\Controllers\DashboardController;
use Workdo\SupportTicket\Http\Controllers\FaqController;
use Workdo\SupportTicket\Http\Controllers\KnowledgebaseCategoryController;
use Workdo\SupportTicket\Http\Controllers\KnowledgeController;
use Workdo\SupportTicket\Http\Controllers\PublicTicketController;
use Workdo\SupportTicket\Http\Controllers\SupportTicketController;
use Workdo\SupportTicket\Http\Controllers\TicketCategoryController;




Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:SupportTicket']], function () {
    Route::resource('support-tickets', SupportTicketController::class);
    Route::resource('ticket-category', TicketCategoryController::class);
    Route::resource('knowledge-category', KnowledgebaseCategoryController::class);
    Route::resource('support-ticket-faq', FaqController::class);
    Route::resource('support-ticket-knowledge', KnowledgeController::class);

    // dashboard

    Route::get('dashboard/support-ticket', [DashboardController::class, 'index'])->name('dashboard.support-tickets');

    Route::post('/custom-fields', [SupportTicketController::class, 'storeCustomFields'])->name('support-ticket.store');

    Route::get('support-tickets/list/search/{status?}', [SupportTicketController::class, 'index'])->name('support-tickets.search');

    Route::delete('support-tickets-attachment/{tid}/destroy/{id}', [SupportTicketController::class, 'attachmentDestroy'])->name('support-tickets.attachment.destroy');

    Route::post('support-ticket/{id}/conversion', [ConversionController::class, 'store'])->name('support-ticket.conversion.store');

    Route::post('support-ticket/{id}/note', [SupportTicketController::class, 'storeNote'])->name('support-ticket.note.store');

    Route::post('support-ticket/setting/store', [SupportTicketController::class, 'setting'])->name('support-ticket.setting.store');

    Route::get('support-ticket/grid/{status?}', [SupportTicketController::class, 'grid'])->name('support-tickets.grid');

    Route::post('support-ticket/getUser', [SupportTicketController::class, 'getUser'])->name('support-tickets.getuser');

    //knowledgebadge import

    Route::get('knowledge/import/export', [KnowledgeController::class, 'fileImportExport'])->name('knowledge.file.import');

    Route::post('knowledge/import', [KnowledgeController::class, 'fileImport'])->name('knowledge.import');

    Route::get('knowledge/import/modal', [KnowledgeController::class, 'fileImportModal'])->name('knowledge.import.modal');

    Route::post('knowledge/data/import/', [KnowledgeController::class, 'knowledgeImportdata'])->name('knowledge.import.data');

    Route::get('faq/import/export', [FaqController::class, 'fileImportExport'])->name('faq.file.import');

    Route::post('faq/import', [FaqController::class, 'fileImport'])->name('faq.import');

    Route::get('faq/import/modal', [FaqController::class, 'fileImportModal'])->name('faq.import.modal');

    Route::post('faq/data/import/', [FaqController::class, 'faqImportdata'])->name('faq.import.data');


    // Faq import

    Route::get('faq/import/export', [FaqController::class, 'fileImportExport'])->name('faq.file.import');

    Route::post('faq/import', [FaqController::class, 'fileImport'])->name('faq.import');

    Route::get('faq/import/modal', [FaqController::class, 'fileImportModal'])->name('faq.import.modal');

    Route::post('faq/data/import/', [FaqController::class, 'faqImportdata'])->name('faq.import.data');
});

// without login route.
Route::middleware(['web'])->group(function () {

    Route::get('{slug}/support-ticket', [PublicTicketController::class, 'create'])->name('support-ticket');

    Route::post('{slug}/ticket-store', [PublicTicketController::class, 'store'])->name('ticket.store');

    Route::get('{slug}/support-ticket/{id}', [PublicTicketController::class, 'index'])->name('ticket.view');

    Route::post('{slug}/support-ticket-reply/{id}', [PublicTicketController::class, 'reply'])->name('ticket.reply');

    Route::get('{slug}/support-ticket-search', [PublicTicketController::class, 'search'])->name('get.ticket.search');

    Route::post('{slug}/support-ticket/post/search', [PublicTicketController::class, 'ticketSearch'])->name('ticket.search', 'uses');

    Route::get('{slug}/faqs', [PublicTicketController::class, 'faq'])->name('faqs');

    Route::get('{slug}/knowledge', [PublicTicketController::class, 'knowledge'])->name('knowledge');

    Route::get('{slug}/knowledgedesc', [PublicTicketController::class, 'knowledgeDescription'])->name('knowledgedesc');
});
