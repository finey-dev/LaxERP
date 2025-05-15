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
use Workdo\Requests\Http\Controllers\RequestsController;
use Workdo\Requests\Http\Controllers\RequestFormFieldController;
use Workdo\Requests\Http\Controllers\RequestResponseController;
use Workdo\Requests\Http\Controllers\RequestCategoryController;
use Workdo\Requests\Http\Controllers\RequestSubcategoryController;

Route::middleware(['web','auth','verified','PlanModuleCheck:Requests'])->group(function (){
    Route::resource('requests',RequestsController::class);
    Route::resource('requests-category',RequestCategoryController::class);
    Route::resource('requests-subcategory',RequestSubcategoryController::class);
    Route::resource('response-requests',RequestResponseController::class);

    Route::get('landingpage/requests', [RequestsController::class, 'requests'])
        ->name('landing.requests');

    Route::post('/request/category', [RequestsController::class, 'request_category'])->name('request.category');
    Route::resource('requests-formfield',RequestFormFieldController::class);
    Route::get('requests-formfield-create/{id}',[RequestFormFieldController::class,'formfield_create'])->name('formfield.create');
    Route::post('requests-formfield-store',[RequestFormFieldController::class,'formfield_store'])->name('formfield.store');

    Route::get('/requests-response-detail/{id}', [RequestResponseController::class, 'responseDetail'])->name('requests.response.detail');

    Route::get('requests-response-show/{id}',[RequestResponseController::class ,'requests_response_show'])->name('requests.response.show');


    Route::get('/request_form_field/{id}',[RequestsController::class, 'requestFieldBind'])->name('request.field.bind');
    Route::post('/request_field_store/{id}', [RequestsController::class, 'requestbindStore'])->name('request.bind.store');
});

Route::middleware(['web'])->group(function (){
    Route::get('requests-response/{code}',[RequestResponseController::class ,'form_show'])->name('response.form.show');
    Route::post('post-response/{code}',[RequestResponseController::class ,'post_response'])->name('post.response');
});
