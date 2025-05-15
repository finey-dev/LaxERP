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
use Workdo\MeetingHub\Http\Controllers\MeetingHubController;
use Workdo\MeetingHub\Http\Controllers\MeetingTypeController;
use Workdo\MeetingHub\Http\Controllers\MeetingMinuteController;
use Workdo\MeetingHub\Http\Controllers\MeetingHubTaskController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:MeetingHub']], function () {
    Route::prefix('meetinghub')->group(function () {

        Route::resource('meetings', MeetingHubController::class);
        Route::resource('meeting-type', MeetingTypeController::class);
        Route::resource('meeting-minutes', MeetingMinuteController::class);
        Route::get('meeting-task.assign/{id}', [MeetingHubTaskController::class, 'assign'])->name('meeting-task.assign');
        Route::get('/{id}/meeting-task/create', [MeetingHubTaskController::class, 'create'])->name('meeting-task.create');
        Route::get('/meeting-task/edit/{id}', [MeetingHubTaskController::class, 'edit'])->name('meeting-task.edit');
        Route::delete('/meeting-task/destroy/{id}', [MeetingHubTaskController::class, 'destroy'])->name('meeting-task.destroy');
        Route::post('/meeting-task/store', [MeetingHubTaskController::class, 'store'])->name('meeting-task.store');
        Route::PUT('/meeting-task/update/{id}', [MeetingHubTaskController::class, 'update'])->name('meeting-task.update');
        Route::post('getcondition', [MeetingHubController::class, 'getcondition'])->name('meetinghub.getcondition');
        Route::post('/getuser', [MeetingHubController::class, 'updateUsersSelect'])->name('meetinghub.updateUsersSelect');
        Route::get('meeting/{id}/minute', [MeetingMinuteController::class, 'meeting_minute'])->name('meetinghub.meeting.minute');
        Route::put('meeting/{id}/minute/update', [MeetingMinuteController::class, 'meeting_minute_update'])->name('meetinghub.meeting.minute.update');
        Route::post('meeting/minute/getNumber', [MeetingMinuteController::class, 'getNumber'])->name('meetinghub.meeting.minute.getNumber');
        Route::post('/send-sms-using-twilio', [MeetingMinuteController::class, 'sendSms'])->name('meetinghub.sendsms');
        Route::post('calculate-duration', [MeetingMinuteController::class, 'calculateDuration'])->name('meetinghub.calculateduration');
        Route::post('meeting/minute/{id}/description/store', [MeetingMinuteController::class, 'descriptionStore'])->name('meeting.minute.description.store');
        Route::post('meeting/minute/{id}/file', [MeetingMinuteController::class, 'fileUpload'])->name('meeting.minute.file.upload');
        Route::get('/meeting/minute/{id}/file/{fid}', [MeetingMinuteController::class, 'fileDownload'])->name('meeting.minute.file.download');
        Route::delete('/meeting/minute/{id}/file/delete/{fid}', [MeetingMinuteController::class, 'fileDelete'])->name('meeting.minute.file.delete');
        Route::post('/meeting/minute/{id}/note', [MeetingMinuteController::class, 'noteStore'])->name('meeting.minute.note.store');
        Route::get('/meeting/minute/{id}/note', [MeetingMinuteController::class, 'noteDestroy'])->name('meeting.minute.note.destroy');
        Route::post('/meeting/minute/{id}/comment', [MeetingMinuteController::class, 'commentStore'])->name('meeting.minute.comment.store');
        Route::get('/meeting/minute/{id}/comment', [MeetingMinuteController::class, 'commentDestroy'])->name('meeting.minute.comment.destroy');
        Route::get('/meetings-report', [MeetingHubController::class, 'report'])->name('meetinghub.meeting.report');
    });
});
