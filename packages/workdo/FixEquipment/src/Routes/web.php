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
use Workdo\FixEquipment\Http\Controllers\AccessoriesController;
use Workdo\FixEquipment\Http\Controllers\AssetsController;
use Workdo\FixEquipment\Http\Controllers\AuditController;
use Workdo\FixEquipment\Http\Controllers\CategoryController;
use Workdo\FixEquipment\Http\Controllers\ComponentController;
use Workdo\FixEquipment\Http\Controllers\ConsumablesController;
use Workdo\FixEquipment\Http\Controllers\DepreciationController;
use Workdo\FixEquipment\Http\Controllers\FixEquipmentController;
use Workdo\FixEquipment\Http\Controllers\LicenceController;
use Workdo\FixEquipment\Http\Controllers\LocationController;
use Workdo\FixEquipment\Http\Controllers\MaintenanceController;
use Workdo\FixEquipment\Http\Controllers\ManufacturerController;
use Workdo\FixEquipment\Http\Controllers\PreDefinedKitController;
use Workdo\FixEquipment\Http\Controllers\StatusController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:FixEquipment']], function () {
    Route::prefix('fixequipment')->group(function () {
        Route::get('dashboard', [FixEquipmentController::class, 'index'])->name('fix.equipment.dashboard');

        Route::get('/', [AssetsController::class, 'index'])->name('fix.equipment.assets.index');
        Route::get('/create', [AssetsController::class, 'create'])->name('fix.equipment.assets.create');
        Route::post('/store', [AssetsController::class, 'store'])->name('fix.equipment.assets.store');
        Route::get('/edit/{id}', [AssetsController::class, 'edit'])->name('fix.equipment.assets.edit');
        Route::post('/update/{id}', [AssetsController::class, 'update'])->name('fix.equipment.assets.update');
        Route::get('/delete/{id}', [AssetsController::class, 'destroy'])->name('fix.equipment.assets.delete');
        Route::get('show/{id}', [AssetsController::class, 'show'])->name('fix.equipment.assets.show');

        Route::get('/locations', [LocationController::class, 'index'])->name('fix.equipment.location.index');
        Route::get('/location/create', [LocationController::class, 'create'])->name('fix.equipment.location.create');
        Route::post('/location/store', [LocationController::class, 'store'])->name('fix.equipment.location.store');
        Route::get('/location/edit/{id}', [LocationController::class, 'edit'])->name('fix.equipment.location.edit');
        Route::any('/location/update/{id}', [LocationController::class, 'update'])->name('fix.equipment.location.update');
        Route::get('/location/delete/{id}', [LocationController::class, 'destroy'])->name('fix.equipment.location.delete');

        Route::get('/depreciation', [DepreciationController::class, 'index'])->name('fix.equipment.depreciation.index');
        Route::get('/depreciation/create', [DepreciationController::class, 'create'])->name('fix.equipment.depreciation.create');
        Route::post('/depreciation/store', [DepreciationController::class, 'store'])->name('fix.equipment.depreciation.store');
        Route::get('/depreciation/edit/{id}', [DepreciationController::class ,'edit'])->name('fix.equipment.depreciation.edit');
        Route::post('/depreciation/update/{id}', [DepreciationController::class, 'update'])->name('fix.equipment.depreciation.update');
        Route::get('/depreciation/delete/{id}', [DepreciationController::class ,'destroy'])->name('fix.equipment.depreciation.delete');

        Route::get('/manufacturer', [ManufacturerController::class, 'index'])->name('fix.equipment.manufacturer.index');
        Route::get('/manufacturer/create', [ManufacturerController::class, 'create'])->name('fix.equipment.manufacturer.create');
        Route::any('/manufacturer/store', [ManufacturerController::class, 'store'])->name('fix.equipment.manufacturer.store');
        Route::get('/manufacturer/edit/{id}', [ManufacturerController::class, 'edit'])->name('fix.equipment.manufacturer.edit');
        Route::post('/manufacturer/update/{id}', [ManufacturerController::class, 'update'])->name('fix.equipment.manufacturer.update');
        Route::get('/manufacturer/delete/{id}', [ManufacturerController::class, 'destroy'])->name('fix.equipment.manufacturer.delete');

        Route::get('/category', [CategoryController::class, 'index'])->name('fix.equipment.category.index');
        Route::get('/category/create', [CategoryController::class, 'create'])->name('fix.equipment.category.create');
        Route::post('/category/store', [CategoryController::class, 'store'])->name('fix.equipment.category.store');
        Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('fix.equipment.category.edit');
        Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('fix.equipment.category.update');
        Route::get('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('fix.equipment.category.delete');

        Route::get('/status', [StatusController::class, 'index'])->name('fix.equipment.status.index');
        Route::get('/status/create', [StatusController::class, 'create'])->name('fix.equipment.status.create');
        Route::post('/status/store', [StatusController::class, 'store'])->name('fix.equipment.status.store');
        Route::get('/status/edit/{id}', [StatusController::class, 'edit'])->name('fix.equipment.status.edit');
        Route::post('/status/update/{id}', [StatusController::class, 'update'])->name('fix.equipment.status.update');
        Route::get('/status/delete/{id}', [StatusController::class, 'destroy'])->name('fix.equipment.status.delete');

        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('fix.equipment.maintenance.index');
        Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('fix.equipment.maintenance.create');
        Route::post('/maintenance/store', [MaintenanceController::class, 'store'])->name('fix.equipment.maintenance.store');
        Route::get('/maintenance/edit/{id}', [MaintenanceController::class, 'edit'])->name('fix.equipment.maintenance.edit');
        Route::post('/maintenance/update/{id}', [MaintenanceController::class, 'update'])->name('fix.equipment.maintenance.update');
        Route::get('/maintenance/delete/{id}', [MaintenanceController::class, 'destroy'])->name('fix.equipment.maintenance.delete');

        Route::get('/pre-defined-kit', [PreDefinedKitController::class, 'index'])->name('fix.equipment.pre.definded.kit.index');
        Route::get('/pre-defined-kit/create', [PreDefinedKitController::class, 'create'])->name('fix.equipment.pre.definded.kit.create');
        Route::post('/pre-defined-kit/store', [PreDefinedKitController::class, 'store'])->name('fix.equipment.pre.definded.kit.store');
        Route::get('/pre-defined-kit/edit/{id}', [PreDefinedKitController::class, 'edit'])->name('fix.equipment.pre.definded.kit.edit');
        Route::post('/pre-defined-kit/update/{id}', [PreDefinedKitController::class, 'update'])->name('fix.equipment.pre.definded.kit.update');
        Route::get('/pre-defined-kit/delete/{id}', [PreDefinedKitController::class, 'destroy'])->name('fix.equipment.pre.definded.kit.delete');

        Route::get('/components', [ComponentController::class, 'index'])->name('fix.equipment.component.index');
        Route::get('/components/create', [ComponentController::class, 'create'])->name('fix.equipment.component.create');
        Route::post('/components/store', [ComponentController::class, 'store'])->name('fix.equipment.component.store');
        Route::get('/components/edit/{id}', [ComponentController::class, 'edit'])->name('fix.equipment.component.edit');
        Route::post('/components/update/{id}', [ComponentController::class, 'update'])->name('fix.equipment.component.update');
        Route::get('/components/delete/{id}', [ComponentController::class, 'destroy'])->name('fix.equipment.component.delete');

        Route::get('/consumables', [ConsumablesController::class ,'index'])->name('fix.equipment.consumables.index');
        Route::get('/consumables/create', [ConsumablesController::class,'create'])->name('fix.equipment.consumables.create');
        Route::post('/consumables/store', [ConsumablesController::class, 'store'])->name('fix.equipment.consumables.store');
        Route::get('/consumables/edit/{id}', [ConsumablesController::class, 'edit'])->name('fix.equipment.consumables.edit');
        Route::post('/consumables/update/{id}', [ConsumablesController::class ,'update'])->name('fix.equipment.consumables.update');
        Route::get('/consumables/delete/{id}', [ConsumablesController::class,'destroy'])->name('fix.equipment.consumables.delete');

        Route::get('/accessories', [AccessoriesController::class ,'index'])->name('fix.equipment.accessories.index');
        Route::get('/accessories/create', [AccessoriesController::class, 'create'])->name('fix.equipment.accessories.create');
        Route::post('/accessories/store', [AccessoriesController::class ,'store'])->name('fix.equipment.accessories.store');
        Route::get('/accessories/edit/{id}', [AccessoriesController::class, 'edit'])->name('fix.equipment.accessories.edit');
        Route::post('/accessories/update/{id}', [AccessoriesController::class, 'update'])->name('fix.equipment.accessories.update');
        Route::get('/accessories/delete/{id}', [AccessoriesController::class, 'destroy'])->name('fix.equipment.accessories.delete');

        Route::get('/licenses', [LicenceController::class, 'index'])->name('fix.equipment.licence.index');
        Route::get('/licenses/create', [LicenceController::class, 'create'])->name('fix.equipment.licence.create');
        Route::post('/licenses/store', [LicenceController::class, 'store'])->name('fix.equipment.licence.store');
        Route::get('/licenses/edit/{id}', [LicenceController::class, 'edit'])->name('fix.equipment.licence.edit');
        Route::post('/licenses/update/{id}', [LicenceController::class, 'update'])->name('fix.equipment.licence.update');
        Route::get('/licenses/delete/{id}', [LicenceController::class, 'destroy'])->name('fix.equipment.licence.delete');

        Route::get('/audit', [AuditController::class, 'index'])->name('fix.equipment.audit.index');
        Route::get('/audit/create', [AuditController::class, 'create'])->name('fix.equipment.audit.create');
        Route::post('/audit/store', [AuditController::class,'store'])->name('fix.equipment.audit.store');
        Route::get('/audit/edit/{id}', [AuditController::class, 'edit'])->name('fix.equipment.audit.edit');
        Route::post('/audit/update/{id}', [AuditController::class, 'update'])->name('fix.equipment.audit.update');
        Route::get('/audit/{id}', [AuditController::class, 'show'])->name('fix.equipment.audit.show');
        Route::get('/audit/delete/{id}', [AuditController::class, 'destroy'])->name('fix.equipment.audit.delete');
        Route::get('/audit/status/{id}', [AuditController::class, 'status'])->name('fix.equipment.audit.status');
        Route::post('/audit/update/status/{id}', [AuditController::class, 'updateStatus'])->name('fix.equipment.audit.status.update');

        Route::get('fix-equipment/audit/getdata', [AuditController::class, 'getData'])->name('audit.get.data');
    });
});
