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
use Workdo\Contract\Http\Controllers\ContractController;
use Workdo\Contract\Http\Controllers\ContractTypeController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Contract']], function () {

    Route::resource('contract_type', ContractTypeController::class);
    Route::resource('contract', ContractController::class);
    Route::get('contract-grid', [ContractController::class, 'grid'])->name('contract.grid');
    Route::post('/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status');

    Route::get('/contract/copy/{id}', [ContractController::class, 'copycontract'])->name('contracts.copy');
    Route::post('/contract/copy/store/{id}', [ContractController::class, 'copycontractstore'])->name('contracts.copy.store');

    Route::post('contract/{id}/description', [ContractController::class, 'descriptionStore'])->name('contracts.description.store');
    Route::post('/contract/{id}/file', [ContractController::class, 'fileUpload'])->name('contracts.file.upload');
    Route::get('/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload'])->name('contracts.file.download');
    Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->name('contracts.file.delete');
    Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('contract.comment.store');
    Route::get('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('contract.comment.destroy');
    Route::post('/contract/{id}/note', [ContractController::class, 'noteStore'])->name('contracts.note.store');
    Route::get('/contract/{id}/note', [ContractController::class, 'noteDestroy'])->name('contracts.note.destroy');

    //renew contact
    Route::get('/contract/{id}/renew_contract', [ContractController::class, 'renewcontract'])->name('contracts.renewcontract');
    Route::post('/contract/{id}/renew_contract/store', [ContractController::class, 'renewcontractstore'])->name('contracts.renewcontract.store');
    Route::delete('/contract/{id}/renew_contract/delete', [ContractController::class, 'renewcontractDelete'])->name('contracts.renewcontract.delete');


    Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract');
    Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');

    Route::get('/signature/{id}', [ContractController::class, 'signature'])->name('signature');
    Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore');

    Route::get('/contract/{id}/mail', [ContractController::class, 'sendmailContract'])->name('send.mail.contract');

    Route::post('contract/setting/store', [ContractController::class, 'setting'])->name('contract.setting.store');

    Route::post('/getproject', [ContractController::class, 'getProject'])->name('getproject');
});
