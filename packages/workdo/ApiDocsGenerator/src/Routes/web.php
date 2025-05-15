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
use Workdo\ApiDocsGenerator\Http\Controllers\ApiDocsGeneratorController;


Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:ApiDocsGenerator']], function () {
    Route::prefix('apidocsgenerator')->group(function () {
       Route::get('/api-docs',[ApiDocsGeneratorController::class,'index'])->name('api.docs');
    });
});
