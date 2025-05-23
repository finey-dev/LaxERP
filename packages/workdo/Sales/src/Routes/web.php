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
use Workdo\Sales\Http\Controllers\ContactController;
use Workdo\Sales\Http\Controllers\SalesController;
use Workdo\Sales\Http\Controllers\StreamController;
use Workdo\Sales\Http\Controllers\OpportunitiesController;
use Workdo\Sales\Http\Controllers\SalesAccountController;
use Workdo\Sales\Http\Controllers\SalesAccountTypeController;
use Workdo\Sales\Http\Controllers\SalesAccountIndustryController;
use Workdo\Sales\Http\Controllers\SalesDocumentController;
use Workdo\Sales\Http\Controllers\SalesDocumentFolderController;
use Workdo\Sales\Http\Controllers\SalesDocumentTypeController;
use Workdo\Sales\Http\Controllers\CallController;
use Workdo\Sales\Http\Controllers\MeetingController;
use Workdo\Sales\Http\Controllers\CommonCaseController;
use Workdo\Sales\Http\Controllers\CaseTypeController;
use Workdo\Sales\Http\Controllers\OpportunitiesStageController;
use Workdo\Sales\Http\Controllers\QuoteController;
use Workdo\Sales\Http\Controllers\ShippingProviderController;
use Workdo\Sales\Http\Controllers\SalesOrderController;
use Workdo\Sales\Http\Controllers\SalesInvoiceController;
use Workdo\Sales\Http\Controllers\ReportController;

Route::middleware(['web'])->group(function () {
    Route::group(['middleware' => ['auth', 'verified','PlanModuleCheck:Sales']], function ()
    {
        // Route::get('dashboard/sales',['as' => 'sales.dashboard','uses' =>'SalesController@index']);
        Route::get('dashboard/sales', [SalesController::class, 'index'])->name('sales.dashboard');


        // Contact
        Route::group(
            [
                'middleware' => [
                    'auth',
                ],
            ], function (){
            Route::get('contact/grid', [ContactController::class,'grid'])->name('contact.grid');
            Route::resource('contact', ContactController::class)->except(['create']);
            Route::get('contact/create/{type}/{id}', [ContactController::class,'create'])->name('contact.create');
        }
        );

         //Contact import
         Route::get('contact/import/export', [ContactController::class,'fileImportExport'])->name('contact.file.import');
         Route::post('contact/import', [ContactController::class,'fileImport'])->name('contact.import');
         Route::get('contact/import/modal', [ContactController::class,'fileImportModal'])->name('contact.import.modal');
         Route::post('contact/data/import/', [ContactController::class,'contactImportdata'])->name('contact.import.data');

        //Stream
        Route::post('streamstore/{type}/{id}/{title}', [StreamController::class,'streamstore'])->name('streamstore');
        Route::resource('stream', StreamController::class);

        // Opportunities

        Route::group(
            [
                'middleware' => [
                    'auth',
                ],
            ], function (){
            Route::get('opportunities/grid', [OpportunitiesController::class,'grid'])->name('opportunities.grid');
            Route::resource('opportunities', OpportunitiesController::class)->except(['create']);
            Route::post('opportunities/change-order', [OpportunitiesController::class,'changeorder'])->name('opportunities.change.order');
            Route::get('opportunities/create/{type}/{id}', [OpportunitiesController::class,'create'])->name('opportunities.create');
        }
        );
        // Opportunities Stage
            Route::resource('opportunities_stage', OpportunitiesStageController::class);

        // Account
            Route::get('salesaccount/grid', [SalesAccountController::class,'grid'])->name('salesaccount.grid');
            Route::resource('salesaccount', SalesAccountController::class)->except(['create']);
            Route::get('salesaccount/create/{type}/{id}', [SalesAccountController::class,'create'])->name('salesaccount.create');

        // Account Type
            Route::resource('account_type', SalesAccountTypeController::class);

        // account industry
            Route::resource('account_industry', SalesAccountIndustryController::class);

        //Sales Account import
        Route::get('salesaccount/import/export', [SalesAccountController::class,'fileImportExport'])->name('salesaccount.file.import');
        Route::post('salesaccount/import', [SalesAccountController::class,'fileImport'])->name('salesaccount.import');
        Route::get('salesaccount/import/modal', [SalesAccountController::class,'fileImportModal'])->name('salesaccount.import.modal');
        Route::post('salesaccount/data/import/', [SalesAccountController::class,'salesaccountImportdata'])->name('salesaccount.import.data');

        // sales document
            Route::get('salesdocument/grid', [SalesDocumentController::class,'grid'])->name('salesdocument.grid');
            Route::resource('salesdocument', SalesDocumentController::class)->except(['create']);
            Route::get('salesdocument/create/{type}/{id}', [SalesDocumentController::class,'create'])->name('salesdocument.create');

        // sales document folder
            Route::resource('salesdocument_folder', SalesDocumentFolderController::class);

        // sales document type
            Route::resource('salesdocument_type', SalesDocumentTypeController::class);

        // Call
            Route::get('call/grid', [CallController::class,'grid'])->name('call.grid');
            Route::post('call/getparent', [CallController::class,'getparent'])->name('call.getparent');
            Route::resource('call', CallController::class)->except(['create']);
            Route::get('call/create/{type}/{id}', [CallController::class,'create'])->name('call.create');

        // Meeting
            Route::get('meeting/grid', [MeetingController::class,'grid'])->name('meeting.grid');
            Route::post('meeting/getparent', [MeetingController::class,'getparent'])->name('meeting.getparent');
            Route::resource('meeting', MeetingController::class)->except(['create']);
            Route::get('meeting/create/{type}/{id}', [MeetingController::class,'create'])->name('meeting.create');

        // Cases
            Route::get('commoncase/grid', [CommonCaseController::class,'grid'])->name('commoncases.grid');
            Route::resource('commoncases', CommonCaseController::class)->except(['create']);
            Route::get('commoncases/create/{type}/{id}', [CommonCaseController::class,'create'])->name('commoncases.create');

        // case type
            Route::resource('case_type', CaseTypeController::class);

        Route::post('/setting/store', [SalesController::class,'setting'])->name('sales.setting.store');
        // Quote
        Route::get('quote/preview/{template}/{color}', [QuoteController::class,'previewQuote'])->name('quote.preview');
        Route::post('quote/template/setting', [QuoteController::class,'saveQuoteTemplateSettings'])->name('quote.template.setting');

        Route::get('quote/grid', [QuoteController::class,'grid'])->name('quote.grid');
        Route::get('quote/{id}/convert', [QuoteController::class,'convert'])->name('quote.convert');
        Route::post('quote/getaccount', [QuoteController::class,'getaccount'])->name('quote.getaccount');
        Route::get('quote/quoteitem/{id}', [QuoteController::class,'quoteitem'])->name('quote.quoteitem');
        Route::post('quote/storeitem/{id}', [QuoteController::class,'storeitem'])->name('quote.storeitem');
        Route::get('quote/quoteitem/edit/{id}', [QuoteController::class,'quoteitemEdit'])->name('quote.quoteitem.edit');
        Route::post('quote/storeitem/edit/{id}', [QuoteController::class,'quoteitemUpdate'])->name('quote.quoteitem.update');
        Route::get('quote/items', [QuoteController::class,'items'])->name('quote.items');
        Route::delete('quote/items/{id}/delete', [QuoteController::class,'itemsDestroy'])->name('quote.items.delete');
        Route::resource('quote', QuoteController::class)->except(['create']);
        Route::get('quote/create/{type}/{id}', [QuoteController::class,'create'])->name('quote.create');

        Route::get('quote/{id}/duplicate', [QuoteController::class,'duplicate'])->name('quote.duplicate');


        Route::get('quote/export', [QuoteController::class,'fileExport'])->name('quote.export');

        // Shipping provider
            Route::resource('shipping_provider', ShippingProviderController::class);


        // Sales order
        Route::get('salesorder/grid', [SalesOrderController::class,'grid'])->name('salesorder.grid');

        Route::get('salesorder/preview/{template}/{color}', [SalesOrderController::class,'previewSalesorder'])->name('salesorder.preview');
        Route::post('salesorder/template/setting', [SalesOrderController::class,'saveSalesorderTemplateSettings'])->name('salesorder.template.setting');


        Route::post('salesorder/getaccount', [SalesOrderController::class,'getaccount'])->name('salesorder.getaccount');
        Route::get('salesorder/salesorderitem/{id}', [SalesOrderController::class,'salesorderitem'])->name('salesorder.salesorderitem');
        Route::post('salesorder/storeitem/{id}', [SalesOrderController::class,'storeitem'])->name('salesorder.storeitem');
        Route::get('salesorder/items', [SalesOrderController::class,'items'])->name('salesorder.items');

        Route::get('salesorder/item/edit/{id}', [SalesOrderController::class,'salesorderItemEdit'])->name('salesorder.item.edit');
        Route::post('salesorder/item/edit/{id}', [SalesOrderController::class,'salesorderItemUpdate'])->name('salesorder.item.update');

        Route::delete('salesorder/items/{id}/delete', [SalesOrderController::class,'itemsDestroy'])->name('salesorder.items.delete');
        Route::resource('salesorder', SalesOrderController::class)->except(['create']);
        Route::get('salesorder/create/{type}/{id}', [SalesOrderController::class,'create'])->name('salesorder.create');
        Route::get('salesorder/{id}/duplicate', [SalesOrderController::class,'duplicate'])->name('salesorder.duplicate');

        // Sales Invocie
        Route::get('salesorder-invoice/{id}', [SalesInvoiceController::class,'salesorder_invoice'])->name('salesorder.invoice');

        Route::get('salesorder-details', [SalesInvoiceController::class,'salesorder_details'])->name('salesorder.details');

        Route::get('salesinvoice/grid', [SalesInvoiceController::class,'grid'])->name('salesinvoice.grid');

        Route::get('salesinvoice/preview/{template}/{color}', [SalesInvoiceController::class,'previewInvoice'])->name('salesinvoice.preview');
        Route::post('salesinvoice/template/setting', [SalesInvoiceController::class,'saveInvoiceTemplateSettings'])->name('salesinvoice.template.setting');


        Route::post('salesinvoice/getaccount', [SalesInvoiceController::class,'getaccount'])->name('salesinvoice.getaccount');
        Route::get('salesinvoice/invoiceitem/{id}', [SalesInvoiceController::class,'invoiceitem'])->name('salesinvoice.invoiceitem');
        Route::post('salesinvoice/storeitem/{id}', [SalesInvoiceController::class,'storeitem'])->name('salesinvoice.storeitem');
        Route::get('salesinvoice/items', [SalesInvoiceController::class,'items'])->name('salesinvoice.items');
        Route::get('salesinvoice/item/edit/{id}', [SalesInvoiceController::class,'invoiceItemEdit'])->name('salesinvoice.item.edit');
        Route::post('salesinvoice/item/edit/{id}', [SalesInvoiceController::class,'invoiceItemUpdate'])->name('salesinvoice.item.update');
        Route::delete('salesinvoice/items/{id}/delete', [SalesInvoiceController::class,'itemsDestroy'])->name('salesinvoice.items.delete');
        Route::resource('salesinvoice', SalesInvoiceController::class)->except(['create']);
        Route::get('salesinvoice/create/{type}/{id}', [SalesInvoiceController::class,'create'])->name('salesinvoice.create');

        Route::get('salesinvoice/{id}/duplicate', [SalesInvoiceController::class,'duplicate'])->name('salesinvoice.duplicate');

        Route::get('salesinvoice/link/{id}', [SalesInvoiceController::class,'invoicelink'])->name('salesinvoice.link');
        Route::post('salesinvoice/send/{id}', [SalesInvoiceController::class,'sendmail'])->name('salesinvoice.sendmail');

        Route::get('invoices-payments', [SalesInvoiceController::class,'payments'])->name('invoices.payments');
        Route::get('invoices/{id}/payments', [SalesInvoiceController::class,'paymentAdd'])->name('invoices.payments.create');
        Route::post('invoices/{id}/payments', [SalesInvoiceController::class,'paymentStore'])->name('invoices.payments.store');

        //Report
        Route::get('report/invoiceanalytic', [ReportController::class,'invoiceanalytic'])->name('report.invoiceanalytic');
        Route::get('report/salesorderanalytic', [ReportController::class,'salesorderanalytic'])->name('report.salesorderanalytic');
        Route::get('report/quoteanalytic', [ReportController::class,'quoteanalytic'])->name('report.quoteanalytic');
    });
    Route::get('quote/pdf/{id}', [QuoteController::class,'pdf'])->name('quote.pdf');

    Route::get('/quote/pay/{quote}', [QuoteController::class, 'payquote'])->name('pay.quote');

    Route::get('/salesinvoice/pay/{invoice}', [SalesInvoiceController::class, 'payinvoice'])->name('pay.salesinvoice');


    Route::get('salesinvoice/pdf/{id}', [SalesInvoiceController::class,'pdf'])->name('salesinvoice.pdf');
    Route::get('/salesorder/pay/{salesorder}', [SalesOrderController::class,'paysalesorder'])->name('pay.salesorder');

    Route::get('salesorder/pdf/{id}', [SalesOrderController::class,'pdf'])->name('salesorder.pdf');
});
