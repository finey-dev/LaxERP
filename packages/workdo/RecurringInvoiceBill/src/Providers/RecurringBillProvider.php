<?php

namespace Workdo\RecurringInvoiceBill\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use Workdo\Account\Entities\Bill;

class RecurringBillProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(){
        view()->composer(['account::bill.create','account::bill.edit','account::bill.show','account::bill.view'], function ($view) {
            $route = \Request::route()->getName();
            if ($route == "bills.create") {
                try {
                    $setting = getCompanyAllSetting();
                    $recurring_invoice_bill = isset($setting['recurring_invoice_bill']) ? $setting['recurring_invoice_bill'] :'off';
                    if ($recurring_invoice_bill == 'on') {
                        $recuuring_type = RecurringInvoiceBill::$recuuring_type;
                        $day_type = RecurringInvoiceBill::$day_type;
                        $view->getFactory()->startPush('recurring_div', view('recurring-invoice-bill::recurring_data.recurring_input',compact('recuuring_type','day_type','route')));
                    }
                } catch (\Throwable $th) {
                }
            }
            if($route == "bill.edit"){
                $ids = \Request::segment(2);
                $id = decrypt($ids);
                $recurring_invoice = RecurringInvoiceBill::where('invoice_id',$id)->where('recurring_type','bill')->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first() ;
                try {
                        $recuuring_type = RecurringInvoiceBill::$recuuring_type;
                        $day_type = RecurringInvoiceBill::$day_type;
                        $view->getFactory()->startPush('recurring_div_edit', view('recurring-invoice-bill::recurring_data.recurring_input_edit',compact('recuuring_type','day_type','recurring_invoice')));

                } catch (\Throwable $th) {
                }
            }
            if($route == "bill.show"){
                $ids = \Request::segment(2);
                $id = decrypt($ids);
                $recuuring_show = RecurringInvoiceBill::where('recurring_type','bill')->where('invoice_id',$id)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();
                if(!empty($recuuring_show->recurring_duration) ){
                    if($recuuring_show){
                        $dublicate_invoice_ids = explode(',',$recuuring_show->dublicate_invoice);
                    }
                    $invoices = Bill::whereIn('id',$dublicate_invoice_ids)->get();
                    $query = Bill::select('bills.*', 'vendors.name as vendor_name')->where('bills.workspace', '=', getActiveWorkSpace())->whereIn('bills.id',$dublicate_invoice_ids);
                    $query = $query->join('vendors', 'bills.vendor_id', '=', 'vendors.id');
                    $invoices = $query->get();
                    $view->getFactory()->startPush('add_recurring_tab', view('recurring-invoice-bill::recurring_data.add_recurring_tab' ,compact('recuuring_show')));
                    $view->getFactory()->startPush('add_recurring_pills', view('recurring-invoice-bill::recurring_data.add_recurring_pills',compact('recuuring_show','invoices')));
                    $view->getFactory()->startPush('recurring_type', view('recurring-invoice-bill::recurring_data.recurring_type',compact('recuuring_show','invoices')));
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
