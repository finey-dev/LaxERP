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
use Workdo\MailBox\Http\Controllers\MailBoxController;

Route::group(['middleware' => ['web','auth','verified','PlanModuleCheck:MailBox']], function () {
    Route::prefix('mailbox')->group(function () {
        Route::post('/setting/store', [MailBoxController::class, 'setting'])->name('mailbox.setting.store');
    });
    Route::post('mailbox/configuration/store', [MailBoxController::class, 'configuration_store'])->name('mailbox.configuration.store');
    Route::any('/mailbox/index/{type?}', [MailBoxController::class, 'index'])->name('mailbox.index');
    Route::get('/mailbox/create/{id?}/{type?}', [MailBoxController::class, 'create'])->name('mailbox.create');
    Route::any('/mailbox/store', [MailBoxController::class, 'store'])->name('mailbox.store');
    Route::any('/mail/view/{folder}/{id}', [MailBoxController::class, 'show'])->name('mailbox.show');
    Route::post('/mailbox/action', [MailBoxController::class, 'action'])->name('mailbox.action');
    Route::any('/mailbox/delete/{folder}/{id}', [MailBoxController::class, 'destroy'])->name('mailbox.destroy');
    Route::get('/mailbox/configuration', [MailBoxController::class, 'configuration'])->name('mailbox.configuration');
    Route::post('/mailbox/reply/sent', [MailBoxController::class, 'reply_send'])->name('mailbox.reply.sent');
    Route::get('/mailbox/mail/reply/{id}/{type}', [MailBoxController::class, 'reply'])->name('mailbox.mail.reply');
    Route::post('/mailbox/mail/move', [MailBoxController::class, 'move_mail'])->name('mailbox.mail.move');
});
