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
use Workdo\Internalknowledge\Http\Controllers\ArticleController;
use Workdo\Internalknowledge\Http\Controllers\BookController;
use Workdo\Internalknowledge\Http\Controllers\MyArticleController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Internalknowledge']], function () {

    Route::get('book/grid', [BookController::class, 'grid'])->name('book.grid');
    Route::resource('book', BookController::class);
    Route::get('article/copy/{id}', [ArticleController::class, 'copyarticle'])->name('article.copy');
    Route::post('article/copy/store/{id}', [ArticleController::class, 'copyarticlestore'])->name('article.copy.store');
    Route::get('article/grid', [ArticleController::class, 'grid'])->name('article.grid');
    Route::get('/article/{id}/description', [ArticleController::class,'description'])->name('article.description');
    Route::get('/book/{id}/description', [BookController::class,'description'])->name('book.description');
    Route::resource('article', ArticleController::class);
    Route::get('mindmap', [ArticleController::class, 'mindmap'])->name('mindmap');
    Route::post('internalknowledge/mindmap/{key}/{id}', [ArticleController::class, 'mindmapSave'])->name('mindmap.save');
    Route::post('mindmap/store', [ArticleController::class, 'mindmapStore'])->name('mindmap.store');
    Route::put('mindmap/update/', [ArticleController::class, 'updateMindmap'])->name('mindmap.update');
    Route::resource('myarticle', MyArticleController::class);
    Route::get('/myarticle/{id}/description', [MyArticleController::class,'description'])->name('myarticle.description');
});

Route::group(['middleware' => ['web']], function () {

    Route::get('book/shared/link/{id}', [BookController::class, 'BookSharedLink'])->name('book.shared.link');
    Route::get('internalknowledge/article/mindmap/{id}', [ArticleController::class, 'mindmapIndex'])->name('internalknowledge.mindmap.index');
    Route::any('internalknowledge/getmindmap/{id}/{k}', [ArticleController::class, 'getMindmap'])->name('internalknowledge.mindmap.get');
    Route::get('article/shared/link/{id}', [ArticleController::class, 'articleSharedLink'])->name('article.shared.link');
    Route::put('mindmap/show/', [ArticleController::class, 'showMindmap'])->name('mindmap.show');
});
