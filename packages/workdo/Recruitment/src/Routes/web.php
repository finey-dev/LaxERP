<?php

use Illuminate\Support\Facades\Route;
use Workdo\Hrm\Http\Controllers\EmployeeController;
use Workdo\Recruitment\Http\Controllers\PayslipTypeController;
use Workdo\Recruitment\Http\Controllers\CustomQuestionController;
use Workdo\Recruitment\Http\Controllers\DashboardController;
use Workdo\Recruitment\Http\Controllers\InterviewScheduleController;
use Workdo\Recruitment\Http\Controllers\JobApplicationController;
use Workdo\Recruitment\Http\Controllers\JobAwardController;
use Workdo\Recruitment\Http\Controllers\JobCandidateCategoryController;
use Workdo\Recruitment\Http\Controllers\JobCandidateController;
use Workdo\Recruitment\Http\Controllers\JobCandidateReferralController;
use Workdo\Recruitment\Http\Controllers\JobCategoryController;
use Workdo\Recruitment\Http\Controllers\JobController;
use Workdo\Recruitment\Http\Controllers\JobExperienceCandidateController;
use Workdo\Recruitment\Http\Controllers\JobExperienceController;
use Workdo\Recruitment\Http\Controllers\JobProjectController;
use Workdo\Recruitment\Http\Controllers\JobQualificationController;
use Workdo\Recruitment\Http\Controllers\JobScreenIndicatorController;
use Workdo\Recruitment\Http\Controllers\JobScreeningTypeController;
use Workdo\Recruitment\Http\Controllers\JobSkillController;
use Workdo\Recruitment\Http\Controllers\JobStageController;
use Workdo\Recruitment\Http\Controllers\JobTemplateController;

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
Route::group(['middleware' => ['web', 'auth', 'verified','PlanModuleCheck:Recruitment']], function () {

    Route::get('dashboard/recruitment', [DashboardController::class, 'index'])->name('recruitment.dashboard');
    Route::resource('job-category', JobCategoryController::class);

    Route::resource('job-stage', JobStageController::class);
    Route::post('job-stage/order', [JobStageController::class, 'order'])->name('job.stage.order');

    Route::resource('jobcandidate-category', JobCandidateCategoryController::class);

    Route::resource('screening-type', JobScreeningTypeController::class);

    Route::resource('screen-indicator', JobScreenIndicatorController::class);

    Route::post('screen/indicator', [CustomQuestionController::class, 'getScreenIndicator'])->name('screen.indicator');

    Route::resource('job', JobController::class);
    Route::get('job-grid', [JobController::class, 'grid'])->name('job.grid');

    Route::get('candidates-job-applications', [JobApplicationController::class, 'archived'])->name('job.application.archived');

    Route::resource('job-candidates', JobCandidateController::class);

    Route::resource('job-project', JobProjectController::class);

    Route::resource('job-qualification', JobQualificationController::class);

    Route::resource('job-award', JobAwardController::class);

    Route::resource('job-experience-candidate', JobExperienceCandidateController::class);

    Route::resource('job-skill', JobSkillController::class);

    Route::resource('job-experience', JobExperienceController::class);

    Route::resource('jobcandidate-referral', JobCandidateReferralController::class);

    Route::resource('job-application', JobApplicationController::class);
    Route::get('job-application-list', [JobApplicationController::class, 'list'])->name('job.list');


    Route::post('job-application/order', [JobApplicationController::class, 'order'])->name('job.application.order');
    Route::post('job-application/{id}/rating', [JobApplicationController::class, 'rating'])->name('job.application.rating');
    Route::delete('job-application/{id}/archive', [JobApplicationController::class, 'archive'])->name('job.application.archive');

    Route::post('job-application/{id}/skill/store', [JobApplicationController::class, 'addSkill'])->name('job.application.skill.store');
    Route::post('job-application/{id}/note/store', [JobApplicationController::class, 'addNote'])->name('job.application.note.store');
    Route::delete('job-application/{id}/note/destroy', [JobApplicationController::class, 'destroyNote'])->name('job.application.note.destroy');
    Route::post('job-application/getByJob', [JobApplicationController::class, 'getByJob'])->name('get.job.application');
    Route::post('job-candidate/getByJobCandiate', [JobApplicationController::class, 'getByJobCandiate'])->name('get.job.candidate');

    Route::get('job-post/{id}', [JobController::class, 'jobPost'])->name('job.post');
    Route::get('job-onboard-grid', [JobApplicationController::class, 'grid'])->name('job.on.board.grid');

    Route::get('job-onboard', [JobApplicationController::class, 'jobOnBoard'])->name('job.on.board');
    Route::get('job-onboard/create/{id}', [JobApplicationController::class, 'jobBoardCreate'])->name('job.on.board.create');
    Route::post('job-onboard/store/{id}', [JobApplicationController::class, 'jobBoardStore'])->name('job.on.board.store');

    Route::get('job-onboard/edit/{id}', [JobApplicationController::class, 'jobBoardEdit'])->name('job.on.board.edit');
    Route::post('job-onboard/update/{id}', [JobApplicationController::class, 'jobBoardUpdate'])->name('job.on.board.update');
    Route::delete('job-onboard/delete/{id}', [JobApplicationController::class, 'jobBoardDelete'])->name('job.on.board.delete');
    Route::get('job-onboard/convert/{id}', [JobApplicationController::class, 'jobBoardConvert'])->name('job.on.board.converts');
    Route::post('job-onboard/convert/{id}', [JobApplicationController::class, 'jobBoardConvertData'])->name('job.on.board.convert');


    Route::post('job-application/stage/change', [JobApplicationController::class, 'stageChange'])->name('job.application.stage.change');

    Route::resource('custom-question', CustomQuestionController::class);


    Route::resource('interview-schedule', InterviewScheduleController::class);
    Route::get('interview-schedule/create/{id?}', [InterviewScheduleController::class, 'create'])->name('interview.schedule.create');

    //payslip type
    Route::resource('paysliptype', PayslipTypeController::class);

    Route::post('employee/getdepartmentes', [EmployeeController::class, 'getDepartment'])->name('employee.getdepartments');
    Route::post('employee/getdesignationes', [EmployeeController::class, 'getdDesignation'])->name('employee.getdesignations');

    //offer Letter
    Route::get('offerletter/index', [JobApplicationController::class, 'offerletterindex'])->name('offerletter.index');
    Route::post('setting/offerletter/{lang?}', [JobApplicationController::class, 'offerletterupdate'])->name('offerletter.update');
    Route::get('job-onboard/pdf/{id}', [JobApplicationController::class, 'offerletterPdf'])->name('offerlatter.download.pdf');
    Route::get('job-onboard/doc/{id}', [JobApplicationController::class, 'offerletterDoc'])->name('offerlatter.download.doc');

    // // job template settig in account
    Route::get('/job/preview/{template}/{color}', [JobCandidateController::class, 'previewJob'])->name('job.preview');
    Route::post('/recruitment/setting/store', [JobCandidateController::class, 'saveJobTemplateSettings'])->name('job.template.setting');

    // Job side
    Route::post('job-attechment/{id}', [JobController::class, 'jobAttechment'])->name('job.file.upload');
    Route::delete('job-attechment/destroy/{id}', [JobController::class, 'jobAttechmentDestroy'])->name('job.attachment.destroy');

    // Job side
    Route::get('job-note/create/{id}', [JobController::class, 'noteCreate'])->name('jobnote.create');
    Route::post('job-note/store/', [JobController::class, 'noteStore'])->name('jobnote.store');
    Route::get('job-note/edit/{id}', [JobController::class, 'noteEdit'])->name('jobnote.edit');
    Route::put('job-note/update/{id}', [JobController::class, 'noteUpdate'])->name('jobnote.update');
    Route::delete('job-note/destroy/{id}', [JobController::class, 'noteDestroy'])->name('jobnote.destroy');
    Route::get('job-note/{id}/description', [JobController::class, 'noteDescription'])->name('jobnote.description');

    // Job side
    Route::get('job-todo/create/{id}', [JobController::class, 'todoCreate'])->name('job-todo.create');
    Route::post('job-todo/store/', [JobController::class, 'todoStore'])->name('job-todo.store');
    Route::get('job-todo/edit/{id}', [JobController::class, 'todoEdit'])->name('job-todo.edit');
    Route::put('job-todo/update/{id}', [JobController::class, 'todoUpdate'])->name('job-todo.update');
    Route::delete('job-todo/destroy/{id}', [JobController::class, 'todoDestroy'])->name('job-todo.destroy');
    Route::get('job-todo/{id}/show', [JobController::class, 'todoShow'])->name('job-todo.show');

    // Job side
    Route::delete('job-activitylog/destroy/{id}', [JobController::class, 'activitylogDestroy'])->name('jobactivitylog.destroy');

    // Job Application side
    Route::post('jobapplication-attechment/{id}', [JobApplicationController::class, 'jobApplicationAttechment'])->name('jobapplication.file.upload');
    Route::delete('jobapplication-attechment/destroy/{id}', [JobApplicationController::class, 'jobApplicationAttechmentDestroy'])->name('jobapplication.attachment.destroy');

    // Job Application side
    Route::get('jobapplication-note/create/{id}', [JobApplicationController::class, 'jobApplicationNoteCreate'])->name('jobapplicationnote.create');
    Route::post('jobapplication-note/store/', [JobApplicationController::class, 'jobApplicationNoteStore'])->name('jobapplicationnote.store');
    Route::get('jobapplication-note/edit/{id}', [JobApplicationController::class, 'jobApplicationNoteEdit'])->name('jobapplicationnote.edit');
    Route::put('jobapplication-note/update/{id}', [JobApplicationController::class, 'jobApplicationNoteUpdate'])->name('jobapplicationnote.update');
    Route::delete('jobapplication-note/destroy/{id}', [JobApplicationController::class, 'jobApplicationNoteDestroy'])->name('jobapplicationnote.destroy');
    Route::get('jobapplication-note/{id}/description', [JobApplicationController::class, 'jobApplicationNoteDescription'])->name('jobapplicationnote.description');

    // Job Application side
    Route::get('jobapplication-todo/create/{id}', [JobApplicationController::class, 'jobApplicationTodoCreate'])->name('jobapplicationtodo.create');
    Route::post('jobapplication-todo/store/', [JobApplicationController::class, 'jobApplicationTodoStore'])->name('jobapplicationtodo.store');
    Route::get('jobapplication-todo/edit/{id}', [JobApplicationController::class, 'jobApplicationTodoEdit'])->name('jobapplicationtodo.edit');
    Route::put('jobapplication-todo/update/{id}', [JobApplicationController::class, 'jobApplicationTodoUpdate'])->name('jobapplicationtodo.update');
    Route::delete('jobapplication-todo/destroy/{id}', [JobApplicationController::class, 'jobApplicationTodoDestroy'])->name('jobapplicationtodo.destroy');
    Route::get('jobapplication-todo/{id}/show', [JobApplicationController::class, 'jobApplicationTodoShow'])->name('jobapplicationtodo.show');

    // Job Application side
    Route::delete('jobapplication-activitylog/destroy/{id}', [JobApplicationController::class, 'jobApplicationActivitylogDestroy'])->name('jobapplicationactivitylog.destroy');

    // Job Candidate side
    Route::post('jobcandidate-attechment/{id}', [JobCandidateController::class, 'jobCandidateAttechment'])->name('jobcandidate.file.upload');
    Route::delete('jobcandidate-attechment/destroy/{id}', [JobCandidateController::class, 'jobCandidateAttechmentDestroy'])->name('jobcandidate.attachment.destroy');

    // Job Candidate side
    Route::get('jobcandidate-note/create/{id}', [JobCandidateController::class, 'jobCandidateNoteCreate'])->name('jobcandidatenote.create');
    Route::post('jobcandidate-note/store/', [JobCandidateController::class, 'jobCandidateNoteStore'])->name('jobcandidatenote.store');
    Route::get('jobcandidate-note/edit/{id}', [JobCandidateController::class, 'jobCandidateNoteEdit'])->name('jobcandidatenote.edit');
    Route::put('jobcandidate-note/update/{id}', [JobCandidateController::class, 'jobCandidateNoteUpdate'])->name('jobcandidatenote.update');
    Route::delete('jobcandidate-note/destroy/{id}', [JobCandidateController::class, 'jobCandidateNoteDestroy'])->name('jobcandidatenote.destroy');
    Route::get('jobcandidate-note/{id}/description', [JobCandidateController::class, 'jobCandidateNoteDescription'])->name('jobcandidatenote.description');

    // Job Candidate side
    Route::get('jobcandidate-todo/create/{id}', [JobCandidateController::class, 'jobCandidateTodoCreate'])->name('jobcandidatetodo.create');
    Route::post('jobcandidate-todo/store/', [JobCandidateController::class, 'jobCandidateTodoStore'])->name('jobcandidatetodo.store');
    Route::get('jobcandidate-todo/edit/{id}', [JobCandidateController::class, 'jobCandidateTodoEdit'])->name('jobcandidatetodo.edit');
    Route::put('jobcandidate-todo/update/{id}', [JobCandidateController::class, 'jobCandidateTodoUpdate'])->name('jobcandidatetodo.update');
    Route::delete('jobcandidate-todo/destroy/{id}', [JobCandidateController::class, 'jobCandidateTodoDestroy'])->name('jobcandidatetodo.destroy');
    Route::get('jobcandidate-todo/{id}/show', [JobCandidateController::class, 'jobCandidateTodoShow'])->name('jobcandidatetodo.show');

    // Job Candidate side
    Route::delete('jobcandidate-activitylog/destroy/{id}', [JobCandidateController::class, 'jobCandidateActivitylogDestroy'])->name('jobcandidateactivitylog.destroy');

    Route::resource('job-template', JobTemplateController::class);
    Route::get('job-template-grid', [JobTemplateController::class, 'grid'])->name('job-template.grid');
    Route::post('job-template/convertToJob', [JobTemplateController::class, 'convertToJob'])->name('job-template.convertToJob');

});

Route::group(['middleware' => 'web'], function () {
    Route::get('career', [JobController::class, 'career'])->name('career');

    Route::get('career/{slug?}/{lang?}', [JobController::class, 'career'])->name('careers');

    // resume show
    Route::get('resume/pdf/{id}', [JobCandidateController::class, 'DownloadResume'])->name('resume.pdf');

    Route::get('job/requirement/{code}/{lang}', [JobController::class, 'jobRequirement'])->name('job.requirement');
    Route::get('job/apply/{code}/{lang}', [JobController::class, 'jobApply'])->name('job.apply');
    Route::get('terms_and_condition/{code}/{lang}', [JobController::class, 'TermsAndCondition'])->name('job.terms.and.conditions');
    Route::post('job/apply/data/{code}', [JobController::class, 'jobApplyData'])->name('job.apply.data');

    Route::get('findjob/{slug}', [JobController::class, 'findJob'])->name('find.job');
    Route::post('trackjob/{slug}', [JobController::class, 'trackJob'])->name('track.job');
});
