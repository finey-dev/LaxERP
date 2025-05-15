<?php

namespace Workdo\RecurringInvoiceBill\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\UpdateBill;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use DateTime;
use DateInterval;


class UpdateRecurringBillLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */


    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateBill $event)
    {
        $request = $event->request;
        $bill = $event->bill;

            if(!empty($request->recurring_duration) && $request->recurring_duration != 'no'){
            if($request->recurring_duration != 'custom'){
                $count_day = explode(' ',$request->recurring_duration);
                $count = $count_day['0'];
                $day = $count_day['1'];

            }else{
                $count = $request->count;
                $day = $request->day_type;
            }
            $date = new DateTime($bill->bill_date);
            $due_date = new DateTime($bill->due_date);
            switch ($day) {
                  case 'day':
                      $date->add(new DateInterval('P'.$count.'D'));
                      $modify_date = $date->format('Y-m-d');

                      $due_date->add(new DateInterval('P'.$count.'D'));
                      $modify_due_date = $due_date->format('Y-m-d');
                      break;
                  case 'month':
                      $date->add(new DateInterval('P'.$count.'M'));
                      $modify_date = $date->format('Y-m-d');

                      $due_date->add(new DateInterval('P'.$count.'M'));
                      $modify_due_date = $due_date->format('Y-m-d');
                      break;
                  case 'year':
                      $date->add(new DateInterval('P'.$count.'Y'));
                      $modify_date = $date->format('Y-m-d');

                      $due_date->add(new DateInterval('P'.$count.'Y'));
                      $modify_due_date = $due_date->format('Y-m-d');
                      break;
                  case 'week':
                      $date->add(new DateInterval('P'.$count.'W'));
                      $modify_date = $date->format('Y-m-d');

                      $due_date->add(new DateInterval('P'.$count.'W'));
                      $modify_due_date = $due_date->format('Y-m-d');
                      break;
            }
            $RecurringInvoiceBill  = RecurringInvoiceBill::where('invoice_id',$bill->id)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->where('recurring_type','bill')->first();
            if(!empty($RecurringInvoiceBill)){
                if(!empty($request->unlimited_cycles) == 1){
                    $cycles ='9999';
                    $pendingcycle = '9999';
                }else{
                    $cycles =$request->cycles;
                    if($request->cycles >= $RecurringInvoiceBill->cycles){
                        $newcycle = $request->cycles - $RecurringInvoiceBill->cycles;
                        $pendingcycle = $newcycle;
                        if($request->cycles >= $RecurringInvoiceBill->pending_cycle ){
                           $pendingcycle = $RecurringInvoiceBill->pending_cycle + $newcycle;
                        }
                    }else{
                        $usecycle = $RecurringInvoiceBill->cycles - $RecurringInvoiceBill->pending_cycle;
                        if($RecurringInvoiceBill->pending_cycle != 0){
                            $pendingcycle = $request->cycles - $usecycle;
                            if($pendingcycle <= 0 ){
                             $pendingcycle = 0;
                            }
                        }else{
                            $pendingcycle = $request->cycles;
                            if($RecurringInvoiceBill->pending_cycle == 0){
                                $pendingcycle = 0;
                            }
                        }
                    }
                }

                $RecurringInvoiceBill->invoice_id           = $bill->id;
                $RecurringInvoiceBill->recurring_type       = 'bill';
                $RecurringInvoiceBill->recurring_duration   = $request->recurring_duration;
                $RecurringInvoiceBill->cycles               = $cycles;
                $RecurringInvoiceBill->day_type             = $day;
                $RecurringInvoiceBill->count                = $count;
                $RecurringInvoiceBill->pending_cycle        = $pendingcycle;
                $RecurringInvoiceBill->modify_date          = $modify_date;
                $RecurringInvoiceBill->modify_due_date      = $modify_due_date;
                $RecurringInvoiceBill->workspace            = getActiveWorkSpace();
                $RecurringInvoiceBill->created_by           = creatorId();
                $RecurringInvoiceBill->save();

            }else{

                $cycles = $request->cycles;
                $pendingcycle =$request->cycles;
                $RecurringInvoiceBill                       =new RecurringInvoiceBill();
                $RecurringInvoiceBill->invoice_id           = $bill->id;
                $RecurringInvoiceBill->recurring_type       = 'bill';
                $RecurringInvoiceBill->recurring_duration   = $request->recurring_duration;
                $RecurringInvoiceBill->cycles               = $cycles;
                $RecurringInvoiceBill->day_type             = $day;
                $RecurringInvoiceBill->count                = $count;
                $RecurringInvoiceBill->pending_cycle        = $pendingcycle;
                $RecurringInvoiceBill->modify_date          = $modify_date;
                $RecurringInvoiceBill->modify_due_date      = $modify_due_date;
                $RecurringInvoiceBill->workspace            = getActiveWorkSpace();
                $RecurringInvoiceBill->created_by           = creatorId();
                $RecurringInvoiceBill->save();
            }
            //
        }
    }
}
