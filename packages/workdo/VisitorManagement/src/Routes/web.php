<?php

use Illuminate\Support\Facades\Route;
use Workdo\Hrm\Http\Controllers\DocumentTypeController;
use Workdo\VisitorManagement\Http\Controllers\ComplianceTypeController;
use Workdo\VisitorManagement\Http\Controllers\VisitorComplianceController;
use Workdo\VisitorManagement\Http\Controllers\PreRegistrationController;
use Workdo\VisitorManagement\Http\Controllers\VisitorBadgeController;
use Workdo\VisitorManagement\Http\Controllers\VisitorDocumentsController;
use Workdo\VisitorManagement\Http\Controllers\VisitorDocumentTypeController;
use Workdo\VisitorManagement\Http\Controllers\VisitorIncidentsController;
use Workdo\VisitorManagement\Http\Controllers\visitorlogcontroller;
use Workdo\VisitorManagement\Http\Controllers\VisitorReportsController;
use Workdo\VisitorManagement\Http\Controllers\VisitorsController;
use Workdo\VisitorManagement\Http\Controllers\VisitorsTimelineController;
use Workdo\VisitorManagement\Http\Controllers\VisitReasonController;

Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:VisitorManagement']], function () {

    Route::resource('/visit-reason',VisitReasonController::class);
    Route::resource('/visitors',VisitorsController::class);
    Route::resource('/visitor-log',visitorlogcontroller::class);
    Route::post('/visitor-log-departure-time/{id}',[visitorlogcontroller::class ,'departure_time'])->name('visitorlog.departuretime');

    Route::post('/get-visitors',[VisitorLogController::class,'getVisitorDetail'])->name('visitor.detail');

    Route::get('/time-line',[VisitorsTimelineController::class,'index'])->name('visitors.timeline');

    Route::get('/visitor/reports',[VisitorReportsController::class,'index'])->name('visitor.reports.index');
    Route::post('/visitors/get-by-date',[VisitorReportsController::class,'getVisitorByDate'])->name('visitor.get.by.date');
    Route::resource('/visitors-badge',VisitorBadgeController::class);
    Route::resource('/visitors-pre-registration',PreRegistrationController::class);
    Route::resource('/visitors-emergency-evacuation',VisitorComplianceController::class);
    Route::resource('/visitors-documents',VisitorDocumentsController::class);
    Route::resource('/visitors-compliance',VisitorComplianceController::class);
    Route::resource('/visitors-incidents',VisitorIncidentsController::class);
    Route::resource('/visitors-compliance-type',ComplianceTypeController::class);
    Route::resource('/visitors-document-type',VisitorDocumentTypeController::class);

});


