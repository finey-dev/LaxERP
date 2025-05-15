<?php

namespace Workdo\RecurringInvoiceBill\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use App\Models\Invoice;

class RecurringInvoiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

     public function boot(){
         view()->composer(['invoice.create','invoice.edit','invoice.view' ], function ($view) {
            if (module_is_active('RecurringInvoiceBill')) {
                $route = \Request::route()->getName();
                $setting = getCompanyAllSetting();
                $recurring_invoice_bill = isset($setting['recurring_invoice_bill']) ? $setting['recurring_invoice_bill'] :'off';
                if ($recurring_invoice_bill == 'on') {
                    if ($route == "invoice.create") {
                        try {
                                $recuuring_type = RecurringInvoiceBill::$recuuring_type;
                                $day_type = RecurringInvoiceBill::$day_type;
                                $view->getFactory()->startPush('add_invoices_field', view('recurring-invoice-bill::recurring_data.recurring_input',compact('recuuring_type','day_type','route')));

                        } catch (\Throwable $th) {
                        }
                    }
                    if($route == "invoice.edit"){
                        $ids = \Request::segment(2);
                        $id = decrypt($ids);
                        $recurring_invoice = RecurringInvoiceBill::where('invoice_id',$id)->where('recurring_type','invoice')->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();
                        try {
                            $recuuring_type = RecurringInvoiceBill::$recuuring_type;
                            $day_type = RecurringInvoiceBill::$day_type;

                            $view->getFactory()->startPush('add_invoices_field', view('recurring-invoice-bill::recurring_data.recurring_input_edit',compact('recuuring_type','day_type','recurring_invoice')));

                        } catch (\Throwable $th) {
                        }


                    }
                    if($route == "invoice.show"){
                        $ids = \Request::segment(2);
                        $id = decrypt($ids);
                        $recuuring_show = RecurringInvoiceBill::where('recurring_type','invoice')->where('invoice_id',$id)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();
                        if(!empty($recuuring_show->recurring_duration) ){
                            if($recuuring_show){
                                $dublicate_invoice_ids = explode(',',$recuuring_show->dublicate_invoice);
                            }
                            $invoices = Invoice::whereIn('id',$dublicate_invoice_ids)->get();
                            $view->getFactory()->startPush('add_recurring_tab', view('recurring-invoice-bill::recurring_data.add_recurring_tab',compact('recuuring_show')));
                            $view->getFactory()->startPush('add_recurring_pills', view('recurring-invoice-bill::recurring_data.add_recurring_pills',compact('recuuring_show','invoices')));
                            $view->getFactory()->startPush('recurring_type', view('recurring-invoice-bill::recurring_data.recurring_type',compact('recuuring_show','invoices')));
                        }
                    }
                }
            }
        });
    }
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
