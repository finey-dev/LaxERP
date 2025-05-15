<?php

namespace Workdo\RecurringInvoiceBill\Listeners;

use App\Events\CreateInvoice;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DateTime;
use DateInterval;



class CreateRecurringInvoiceLis
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
    public function handle(CreateInvoice $event)
    {
       $request = $event->request;
       $invoice = $event->invoice;
       if(!empty($request->recurring_duration) && $request->recurring_duration != 'no'){
          if($request->recurring_duration != 'custom'){
             $count_day = explode(' ',$request->recurring_duration);
             $count = $count_day['0'];
             $day = $count_day['1'];

          }else{
            $count = $request->count;
            $day = $request->day_type;
          }
          $date = new DateTime($invoice->issue_date);
          $due_date = new DateTime($invoice->due_date);
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
        if(!empty($request->unlimited_cycles) == 1){
            $cycles ='9999';
            $pending_cycle = '9999';
        }else{
            $cycles =$request->cycles;
            $pending_cycle = $request->cycles;
        }
        $RecurringInvoiceBill                       = new RecurringInvoiceBill();
        $RecurringInvoiceBill->invoice_id           = $invoice->id;
        $RecurringInvoiceBill->recurring_type       = 'invoice';
        $RecurringInvoiceBill->recurring_duration   = $request->recurring_duration;
        $RecurringInvoiceBill->cycles               = $pending_cycle;
        $RecurringInvoiceBill->day_type             = $day;
        $RecurringInvoiceBill->count                = $count;
        $RecurringInvoiceBill->pending_cycle        = $cycles;
        $RecurringInvoiceBill->modify_date          = $modify_date;
        $RecurringInvoiceBill->modify_due_date      = $modify_due_date;
        $RecurringInvoiceBill->workspace            = getActiveWorkSpace();
        $RecurringInvoiceBill->created_by           = creatorId();
        $RecurringInvoiceBill->save();
       }
    }
}
