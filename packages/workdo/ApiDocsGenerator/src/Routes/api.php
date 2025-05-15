<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Workdo\ApiDocsGenerator\Http\Controllers\AccountingController;
use Workdo\ApiDocsGenerator\Http\Controllers\AuthController;
use Workdo\ApiDocsGenerator\Http\Controllers\CrmController;
use Workdo\ApiDocsGenerator\Http\Controllers\WorkSpaceApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\UserApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\InvoiceApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\ProjectApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\ProposalApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\LeadApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\DealApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\HrmApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\PosApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\ProductAndServiceApiController;
use Workdo\ApiDocsGenerator\Http\Controllers\SalesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/apidocsgenerator', function (Request $request) {

//  return $request->user();
// });

Route::middleware(['custom.jwt'])->get('/apidocsgenerator', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class,'login']);
Route::post('/auth-logout',[AuthController::class,'logout']);
Route::post('/refresh',[AuthController::class,'refresh']);

Route::middleware(['custom.jwt'])->group(function () {

    // workspace api
    Route::get('/workspaces',[WorkSpaceApiController::class,'index']);
    Route::post('/workspace/store',[WorkSpaceApiController::class,'store']);
    Route::get('workspace/show/{id}',[WorkSpaceApiController::class,'show']);
    Route::put('workspace/update/{id}',[WorkSpaceApiController::class,'update']);
    Route::delete('workspace/delete/{id}',[WorkSpaceApiController::class,'destroy']);
    Route::get('workspace/change/{id}',[WorkSpaceApiController::class,'change']);

    Route::prefix('{slug}')->group(function () {
        Route::get('users',[UserApiController::class,'index']);

        Route::get('invoices',[InvoiceApiController::class,'index']);
        Route::get('invoice/show/{invoice_id}',[InvoiceApiController::class,'show']);
        Route::get('invoice/create/{type}',[InvoiceApiController::class,'create']);

        Route::get('projects',[ProjectApiController::class,'index']);
        Route::post('projects/create',[ProjectApiController::class,'store']);
        Route::get('project/show/{id}',[ProjectApiController::class,'show']);
        Route::put('project/update/{id}',[ProjectApiController::class,'update']);
        Route::delete('project/delete/{id}',[ProjectApiController::class,'destroy']);

        Route::get('project/milestone/{id}',[ProjectApiController::class,'getMilestone']);
        Route::post('project/{id}/task/create',[ProjectApiController::class,'taskStore']);
        Route::get('project/{id}/task-board',[ProjectApiController::class,'taskBoard']);
        Route::put('project/{id}/task/update/{task_id}',[ProjectApiController::class,'taskUpdate']);
        Route::get('project/task/show/{id}',[ProjectApiController::class,'taskShow']);
        Route::delete('project/{project_id}/task/delete/{task_id}',[ProjectApiController::class,'taskDelete']);

        Route::get('bug-status-list',[ProjectApiController::class,'bugStatusList']);
        Route::get('project/bug-report/{id}',[ProjectApiController::class,'bugReport']);
        Route::post('project/bug-report/create/{project_id}',[ProjectApiController::class,'bugStore']);
        Route::get('project/bug-report/show/{id}',[ProjectApiController::class,'bugReportShow']);
        Route::put('project/{project_id}/bug-report/update/{bug_id}',[ProjectApiController::class,'bugUpdate']);
        Route::delete('project/{project_id}/bug-report/delete/{bug_id}',[ProjectApiController::class,'bugDestroy']);

        Route::get('proposals',[ProposalApiController::class,'index']);
        Route::get('proposal/show/{id}',[ProposalApiController::class,'show']);

        Route::get('leads',[LeadApiController::class,'index']);
        Route::post('lead/create',[LeadApiController::class,'store']);
        Route::get('lead/show/{id}',[LeadApiController::class,'show']);
        Route::put('lead/update/{id}',[LeadApiController::class,'update']);
        Route::delete('lead/delete/{id}',[LeadApiController::class,'destroy']);

        Route::post('lead-task/store/{lead_id}',[LeadApiController::class,'leadTaskStore']);
        Route::put('lead-task/{lead_id}/update/{lead_task_id}',[LeadApiController::class,'leadTaskUpdate']);
        Route::delete('lead-task/{lead_id}/delete/{lead_task_id}',[LeadApiController::class,'leadTaskDelete']);

        Route::post('lead-user/store/{lead_id}',[LeadApiController::class,'leadUserStore']);
        Route::delete('lead-user/{lead_id}/delete/{lead_user_id}',[LeadApiController::class,'leadUserDelete']);

        Route::get('lead-product/create/{lead_id}',[LeadApiController::class,'leadProductCreate']);
        Route::post('lead-product/store/{lead_id}',[LeadApiController::class,'leadProductStore']);
        Route::delete('lead-product/{lead_id}/delete/{lead_product_id}',[LeadApiController::class,'leadProductDelete']);

        Route::post('lead-source/create/{lead_id}',[LeadApiController::class,'leadSourceCreate']);
        Route::delete('lead-source/{lead_id}/delete/{lead_source_id}',[LeadApiController::class,'leadSourceDelete']);

        Route::post('lead-email/create/{lead_id}',[LeadApiController::class,'leadEmailCreate']);
        Route::post('lead-discussion/create/{lead_id}',[LeadApiController::class,'leadDiscussionCreate']);
        Route::post('lead-note/create/{lead_id}',[LeadApiController::class,'leadNoteCreate']);

        Route::post('lead-call/create/{lead_id}',[LeadApiController::class,'leadCallCreate']);
        Route::put('lead-call/{lead_id}/update/{lead_call_id}',[LeadApiController::class,'leadCallEdit']);
        Route::delete('lead-call/{lead_id}/delete/{lead_call_id}',[LeadApiController::class,'leadCallDelete']);

        Route::post('lead-file/create/{lead_id}',[LeadApiController::class,'leadFileCreate']);
        Route::delete('lead-file/{lead_id}/delete/{lead_file_id}',[LeadApiController::class,'leadFileDelete']);

        Route::get('deals',[DealApiController::class,'index']);
        Route::get('deal/show/{id}',[DealApiController::class,'show']);

        Route::get('pipelines',[CrmController::class,'pipelinesList']);
        Route::post('pipeline/create',[CrmController::class,'pipelineCreate']);
        Route::put('pipeline/update/{id}',[CrmController::class,'pipelineUpdate']);
        Route::delete('pipeline/delete/{id}',[CrmController::class,'pipelineDelete']);

        Route::get('lead/stages',[CrmController::class,'leadStages']);
        Route::post('lead/stage/create',[CrmController::class,'leadStageCreate']);
        Route::put('lead/stage/update/{id}',[CrmController::class,'leadStageUpdate']);
        Route::delete('lead/stage/delete/{id}',[CrmController::class,'leadStageDelete']);

        Route::get('deal/stages',[CrmController::class,'dealStages']);
        Route::post('deal/stage/create',[CrmController::class,'dealStageCreate']);
        Route::put('deal/stage/update/{id}',[CrmController::class,'dealStageUpdate']);
        Route::delete('deal/stage/delete/{id}',[CrmController::class,'dealStageDelete']);

        Route::get('labels',[CrmController::class,'labelsList']);
        Route::post('label/create',[CrmController::class,'labelCreate']);
        Route::put('label/update/{id}',[CrmController::class,'labelUpdate']);
        Route::delete('label/delete/{id}',[CrmController::class,'labelDelete']);

        Route::get('sources',[CrmController::class,'sourcesList']);
        Route::post('source/create',[CrmController::class,'sourceCreate']);
        Route::put('source/update/{id}',[CrmController::class,'sourceUpdate']);
        Route::delete('source/delete/{id}',[CrmController::class,'sourceDelete']);

        Route::get('product/category',[ProductAndServiceApiController::class,'categoryList']);
        Route::post('product/category/create',[ProductAndServiceApiController::class,'categoryStore']);
        Route::put('product/category/update/{id}',[ProductAndServiceApiController::class,'categoryUpdate']);
        Route::delete('product/category/delete/{id}',[ProductAndServiceApiController::class,'categoryDelete']);

        Route::get('invoice/category',[ProductAndServiceApiController::class,'invoiceCategoryList']);
        Route::post('invoice/category/create',[ProductAndServiceApiController::class,'invoiceCategoryStore']);
        Route::put('invoice/category/update/{id}',[ProductAndServiceApiController::class,'invoiceCategoryUpdate']);
        Route::delete('invoice/category/delete/{id}',[ProductAndServiceApiController::class,'invoiceCategoryDelete']);

        Route::get('bill/category',[ProductAndServiceApiController::class,'billCategoryList']);
        Route::post('bill/category/create',[ProductAndServiceApiController::class,'billCategoryStore']);
        Route::put('bill/category/update/{id}',[ProductAndServiceApiController::class,'billCategoryUpdate']);
        Route::delete('bill/category/delete/{id}',[ProductAndServiceApiController::class,'billCategoryDelete']);

        Route::get('tax',[ProductAndServiceApiController::class,'taxList']);
        Route::post('tax/create',[ProductAndServiceApiController::class,'taxCreate']);
        Route::put('tax/update/{id}',[ProductAndServiceApiController::class,'taxUpdate']);
        Route::delete('tax/delete/{id}',[ProductAndServiceApiController::class,'taxDelete']);

        Route::get('units',[ProductAndServiceApiController::class,'unitList']);
        Route::post('unit/create',[ProductAndServiceApiController::class,'unitStore']);
        Route::put('unit/update/{id}',[ProductAndServiceApiController::class,'unitUpdate']);
        Route::delete('unit/delete/{id}',[ProductAndServiceApiController::class,'unitDelete']);

        Route::get('products',[ProductAndServiceApiController::class,'products']);
        Route::get('product-detail/{id}',[ProductAndServiceApiController::class,'showProduct']);

        Route::get('customers',[AccountingController::class,'customers']);
        Route::post('customer/create',[AccountingController::class,'customerStore']);
        Route::get('customer/{id}',[AccountingController::class,'customerDetail']);
        Route::put('customer/update/{id}',[AccountingController::class,'customerUpdate']);
        Route::delete('customer/delete/{id}',[AccountingController::class,'customerDelete']);

        Route::get('vendors',[AccountingController::class,'vendors']);
        Route::post('vendor/create',[AccountingController::class,'vendorStore']);
        Route::get('vendor/{id}',[AccountingController::class,'vendorDetail']);
        Route::put('vendor/update/{id}',[AccountingController::class,'vendorUpdate']);
        Route::delete('vendor/delete/{id}',[AccountingController::class,'vendorDelete']);

        Route::get('bank-accounts',[AccountingController::class,'bankAccountList']);
        Route::post('bank-account/create',[AccountingController::class,'bankAccountStore']);
        Route::put('bank-account/update/{id}',[AccountingController::class,'bankAccountUpdate']);
        Route::delete('bank-account/delete/{id}',[AccountingController::class,'bankAccountDelete']);

        Route::get('chart-of-accounts',[AccountingController::class,'chartOfAccount']);
        Route::get('chart-of-account-sub-types',[AccountingController::class,'chartOfAccountSubTypeList']);
        Route::post('chart-of-account/create',[AccountingController::class,'chartOfAccountStore']);
        Route::put('chart-of-account/update/{id}',[AccountingController::class,'chartOfAccountUpdate']);
        Route::delete('chart-of-account/delete/{id}',[AccountingController::class,'chartOfAccountDelete']);

        Route::get('bank-transfer',[AccountingController::class,'bankTransfer']);
        Route::post('bank-transfer/create',[AccountingController::class,'bankTransferStore']);
        Route::put('bank-transfer/update/{id}',[AccountingController::class,'bankTransferUpdate']);
        Route::delete('bank-transfer/delete/{id}',[AccountingController::class,'bankTransferDelete']);

        Route::get('revenues',[AccountingController::class,'revenueList']);
        Route::post('revenue/create',[AccountingController::class,'revenueStore']);
        Route::put('revenue/update/{id}',[AccountingController::class,'revenueUpdate']);
        Route::delete('revenue/delete/{id}',[AccountingController::class,'revenueDelete']);

        Route::get('customer-credit-notes',[AccountingController::class,'customerCreditNoteList']);
        Route::post('customer-credit-notes/create',[AccountingController::class,'customerCreditNoteStore']);
        Route::put('customer-credit-notes/update/{id}',[AccountingController::class,'customerCreditNoteUpdate']);
        Route::delete('customer-credit-notes/delete/{id}',[AccountingController::class,'customerCreditNoteDelete']);

        Route::get('bills',[AccountingController::class,'billsList']);
        Route::post('bill/create',[AccountingController::class,'billStore']);
        Route::get('bill-detail/{id}',[AccountingController::class,'billDetail']);
        Route::put('bill/update/{id}',[AccountingController::class,'billUpdate']);
        Route::delete('bill/delete/{id}',[AccountingController::class,'billDelete']);

        Route::get('payments',[AccountingController::class,'paymentList']);
        Route::post('payment/create',[AccountingController::class,'paymentStore']);
        Route::put('payment/update/{id}',[AccountingController::class,'paymentUpdate']);
        Route::delete('payment/delete/{id}',[AccountingController::class,'paymentDelete']);

        Route::get('debit-note',[AccountingController::class,'debitNoteList']);
        Route::post('debit-note/create',[AccountingController::class,'debitNoteStore']);
        Route::put('debit-note/update/{id}',[AccountingController::class,'debitNoteUpdate']);
        Route::delete('debit-note/delete/{id}',[AccountingController::class,'debitNoteDelete']);

        Route::get('pos-orders',[PosApiController::class,'posOrderList']);

        Route::get('employees',[HrmApiController::class,'employees']);
        Route::get('emoployee-detail/{id}',[HrmApicontroller::class,'employeeDetail']);

        Route::get('salary',[HrmApiController::class,'salary']);
        Route::get('salary-detail/{id}',[HrmApiController::class,'salaryDetail']);

        Route::get('attendance',[HrmApiController::class,'attendanceList']);
        Route::get('leaves',[HrmApiController::class,'leavesList']);

        Route::get('awards',[HrmApiController::class, 'awardsList']);
        Route::get('transfers',[HrmApiController::class,'transferList']);
        Route::get('resignations',[HrmApiController::class,'resignationsList']);
        Route::get('holidays',[HrmApiController::class,'holidaysList']);
        Route::get('events',[HrmApiController::class,'eventsList']);
        Route::get('documents',[HrmApiController::class,'documentsList']);
        Route::get('company-policies',[HrmApiController::class,'companyPolicyList']);
        Route::get('branches',[HrmApiController::class,'branchesList']);
        Route::get('departments',[HrmApiController::class,'departmentsList']);
        Route::get('designations',[HrmApiController::class,'designationsList']);

        Route::get('sales-accounts',[SalesController::class,'salesAccounts']);
        Route::post('sales-account/create',[SalesController::class,'salesAccountStore']);
        Route::put('sales-account/update/{id}',[SalesController::class,'salesAccountUpdate']);
        Route::delete('sales-account/delete/{id}',[SalesController::class,'salesAccountDelete']);

        Route::get('sales-contacts',[SalesController::class,'salesContacts']);
        Route::post('sales-contact/create',[SalesController::class,'salesContactCreate']);
        Route::put('sales-contact/update/{id}',[SalesController::class,'salesContactUpdate']);
        Route::delete('sales-contact/delete/{id}',[SalesController::class,'salesContactDelete']);

        Route::get('sales/account/type',[SalesController::class,'salesAccountType']);
        Route::post('sales/account/type/create',[SalesController::class,'salesAccountTypeCreate']);
        Route::put('sales/account/type/update/{id}',[SalesController::class,'salesAccountTypeUpdate']);
        Route::delete('sales/account/type/delete/{id}',[SalesController::class,'salesAccountTypeDelete']);

        Route::get('sales/account/industry',[SalesController::class,'salesAccountIndustry']);
        Route::post('sales/account/industry/create',[SalesController::class,'salesAccountIndustryCreate']);
        Route::put('sales/account/industry/update/{id}',[SalesController::class,'salesAccountIndustryUpdate']);
        Route::delete('sales/account/industry/delete/{id}',[SalesController::class,'salesAccountIndustryDelete']);

        Route::get('sales/opportunity/stage',[SalesController::class,'salesOpportunity']);
        Route::post('sales/opportunity/stage/create',[SalesController::class,'salesOpportunityCreate']);
        Route::put('sales/opportunity/stage/update/{id}',[SalesController::class,'salesOpportunityUpdate']);
        Route::delete('sales/opportunity/stage/delete/{id}',[SalesController::class,'salesOpportunityDelete']);

        Route::get('sales/case-type',[SalesController::class,'salesCaseType']);
        Route::post('sales/case-type/create',[SalesController::class,'salesCaseTypeCreate']);
        Route::put('sales/case-type/update/{id}',[SalesController::class,'salesCaseTypeUpdate']);
        Route::delete('sales/case-type/delete/{id}',[SalesController::class,'salesCaseTypeDelete']);

        Route::get('sales/shipping/provider',[SalesController::class,'salesShippingProvider']);
        Route::post('sales/shipping/provider/create',[SalesController::class,'salesShippingProviderCreate']);
        Route::put('sales/shipping/provider/update/{id}',[SalesController::class,'salesShippingProviderUpdate']);
        Route::delete('sales/shipping/provider/delete/{id}',[SalesController::class,'salesShippingProviderDelete']);

        Route::get('sales/document-type',[SalesController::class,'salesDocumentType']);
        Route::post('sales/document-type/create',[SalesController::class,'salesDocumentTypeCreate']);
        Route::put('sales/document-type/update/{id}',[SalesController::class,'salesDocumentTypeUpdate']);
        Route::delete('sales/document-type/delete/{id}',[SalesController::class,'salesDocumentTypeDelete']);

        Route::get('sales/document/folder',[SalesController::class,'salesDocumentFolder']);
        Route::post('sales/document/folder/create',[SalesController::class,'salesDocumentFolderCreate']);
        Route::put('sales/document/folder/update/{id}',[SalesController::class,'salesDocumentFolderUpdate']);
        Route::delete('sales/document/folder/delete/{id}',[SalesController::class,'salesDocumentFolderDelete']);

        Route::get('sales/opportunities',[SalesController::class,'salesOpportunities']);
        Route::post('sales/opportunities/create',[SalesController::class,'salesOpportunitiesCreate']);
        Route::put('sales/opportunity/update/{id}',[SalesController::class,'salesOpportunitiesUpdate']);
        Route::delete('sales/opportunity/delete/{id}',[SalesController::class,'salesOpportunitiesDelete']);

        Route::get('sales/quotes',[SalesController::class,'salesQuotes']);
        Route::post('sales/quotes/create',[SalesController::class,'salesQuotesCreate']);
        Route::put('sales/quote/update/{id}',[SalesController::class,'salesQuotesUpdate']);
        Route::delete('sales/quote/delete/{id}',[SalesController::class,'salesQuoteDelete']);

        Route::get('sales/cases',[SalesController::class,'salesCases']);
        Route::post('sales/cases/create',[SalesController::class,'salesCaseCreate']);
        Route::put('sales/cases/update/{id}',[SalesController::class,'salesCasesUpdate']);
        Route::delete('sales/cases/delete/{id}',[SalesController::class,'salesCaseDelete']);

        Route::get('sales/documents',[SalesController::class,'salesDocuments']);
        Route::post('sales/documents/create',[SalesController::class,'salesDocumentsCreate']);
        Route::put('sales/document/update/{id}',[SalesController::class,'salesDocumentsUpdate']);
        Route::delete('sales/document/delete/{id}',[SalesController::class,'salesDocumentsDelete']);
    });
});
