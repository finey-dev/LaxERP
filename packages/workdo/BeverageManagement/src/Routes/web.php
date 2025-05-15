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
use Workdo\BeverageManagement\Http\Controllers\BeverageMaintenanceController;
use Workdo\BeverageManagement\Http\Controllers\BillOfMaterialController;
use Workdo\BeverageManagement\Http\Controllers\CollectionCenterController;
use Workdo\BeverageManagement\Http\Controllers\CollectionCenterStockController;
use Workdo\BeverageManagement\Http\Controllers\ManufacturingController;
use Workdo\BeverageManagement\Http\Controllers\PackagingController;
use Workdo\BeverageManagement\Http\Controllers\QualityChecksController;
use Workdo\BeverageManagement\Http\Controllers\RawMaterialController;
use Workdo\BeverageManagement\Http\Controllers\QualityStandardsController;
use Workdo\BeverageManagement\Http\Controllers\WasteRecordController;

Route::group(['middleware' => ['web','auth','verified','PlanModuleCheck:BeverageManagement']], function ()
{
    Route::resource('collection-center', CollectionCenterController::class);
    Route::resource('raw-material', RawMaterialController::class);
    Route::post('raw-material/item', [RawMaterialController::class,'getProductItem'])->name('raw_material.item');
    Route::get('raw-material/qty/transfer', [RawMaterialController::class,'wherehourseQtyTransfer'])->name('raw_material.qty.wherehouse');
    Route::get('collection-center/qty/transfer/{id}/{type}', [RawMaterialController::class,'collectionCenterQtyTransfer'])->name('collection-center.qty.transfer');
    Route::post('collection-center/qty/transfer/store', [RawMaterialController::class,'collectionCenterQtyStore'])->name('collection-center.qty.store');
    Route::post('collection-center/move', [RawMaterialController::class,'collectionCenterMove'])->name('collection-center.move');

    Route::resource('bill-of-material', BillOfMaterialController::class);
    Route::post('center/raw/meterials',         [BillOfMaterialController::class,'centerWiseRawMaterial'])->name('center.raw.material');
    Route::post('raw/meterials',         [BillOfMaterialController::class,'getRawMaterial'])->name('raw.material');
    Route::get('raw/meterials/items',         [BillOfMaterialController::class,'items'])->name('raw.material.items');
    Route::post('raw/meterial/delete',         [BillOfMaterialController::class,'deleteRawMaterial'])->name('raw.material.delete');
    Route::resource('manufacturing',  ManufacturingController::class);
    Route::post('bill/material',  [ManufacturingController::class,'billMaterial'])->name('bill.material');
    Route::get('status/completed/{id}',  [ManufacturingController::class,'statusCompleted'])->name('manufacture.status');
    Route::resource('packaging',  PackagingController::class);
    Route::post('package/bill/material',  [PackagingController::class,'billMaterial'])->name('package.bill.material');
    Route::get('package/status/completed/{id}',  [PackagingController::class,'statusCompleted'])->name('package.status');
    Route::post('packaging/center/raw/meterials',         [PackagingController::class,'centerWiseRawMaterial'])->name('package.center.raw.material');
    Route::get('packaging/raw/meterials/items',         [PackagingController::class,'items'])->name('package.raw.material.items');
    Route::post('packaging/raw/meterial/delete',         [PackagingController::class,'deleteRawMaterial'])->name('package.raw.material.delete');
    Route::get('collection/add-stock/{id}',  [CollectionCenterStockController::class,'create'])->name('add-stock.create');
    Route::get('collection/packaging/add-stock/{id}',  [CollectionCenterStockController::class,'showForm'])->name('add-stock.show.form');
    Route::post('collection/packaging/add-stock/store',  [CollectionCenterStockController::class,'addStockPackaging'])->name('add-stock.packaging.store');
    Route::post('collection/add-stock/store',  [CollectionCenterStockController::class,'store'])->name('add-stock.store');
    Route::post('warehouse/item',  [CollectionCenterStockController::class,'warehouseItem'])->name('warehouse.item');
    Route::post('item/collectiomn-center',  [CollectionCenterStockController::class,'itemCollectionCenter'])->name('item.collection.center');

    Route::resource('quality-standards', QualityStandardsController::class);
    Route::resource('quality-checks', QualityChecksController::class);
    Route::resource('beverage-maintenance', BeverageMaintenanceController::class);
    Route::resource('waste-records', WasteRecordController::class);


});
