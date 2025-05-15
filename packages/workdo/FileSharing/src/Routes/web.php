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
use Workdo\FileSharing\Http\Controllers\DownloadController;
use Workdo\FileSharing\Http\Controllers\FilesController;
use Workdo\FileSharing\Http\Controllers\FileSharingVerificationController;
use Workdo\FileSharing\Http\Controllers\FileTrashController;

Route::group(['middleware' => ['web', 'auth', 'verified', 'PlanModuleCheck:FileSharing']], function () {
    Route::resource('files', FilesController::class);
    Route::resource('download-detailes', DownloadController::class);
    Route::get('file/grid', [FilesController::class, 'grid'])->name('file.grid');
    Route::resource('file-verification', FileSharingVerificationController::class);
    Route::resource('files-trash', FileTrashController::class);
    Route::get('file/restore/{id}', [FileTrashController::class, 'restore'])->name('file.restore');

});
Route::middleware(['web'])->group(function () {
    Route::post('/download/{file}', [FilesController::class, 'download'])->name('file.download');

    Route::post('file/password/check/{id}/{lang?}', [FilesController::class, 'PasswordCheck'])->name('file.password.check');

    Route::get('file/shared/link/{id}/{lang?}', [FilesController::class, 'FileSharedLink'])->name('file.shared.link');

    Route::delete('file-verification-request/{id}', [FileSharingVerificationController::class, 'destroyRequest'])->name('file.request.delete');
});
