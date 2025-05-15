<?php

namespace Workdo\RecurringInvoiceBill\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use Workdo\Account\Events\CreateBill;
use DateTime;
use DateInterval;

class CreateRecurringBillLis
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
    public function handle(CreateBill $event)
    {
        //
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
            $RecurringInvoiceBill                      = new RecurringInvoiceBill();
            $RecurringInvoiceBill->invoice_id          = $bill->id;
            $RecurringInvoiceBill->recurring_type      = 'bill';
            $RecurringInvoiceBill->recurring_duration  = $request->recurring_duration;
            $RecurringInvoiceBill->cycles              = $request->cycles;
            $RecurringInvoiceBill->day_type            = $day;
            $RecurringInvoiceBill->count               = $count;
            $RecurringInvoiceBill->pending_cycle       = $request->cycles;
            $RecurringInvoiceBill->modify_date         = $modify_date;
            $RecurringInvoiceBill->modify_due_date     = $modify_due_date;
            $RecurringInvoiceBill->workspace           = getActiveWorkSpace();
            $RecurringInvoiceBill->created_by          = creatorId();
            $RecurringInvoiceBill->save();
        }
    }
}
