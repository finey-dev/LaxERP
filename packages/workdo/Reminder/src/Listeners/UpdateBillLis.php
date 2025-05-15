<?php

namespace Workdo\Reminder\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\UpdateBill;
use Workdo\Reminder\Entities\Reminder;
use DateTime;
use DateInterval;


class UpdateBillLis
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
        $bill = $event->bill;
        $Reminderinvoice = Reminder::where('module_value' ,$bill->id)->first();
        if(!empty($Reminderinvoice)){
            $dateString = $bill->due_date;
            $date = new DateTime($dateString);
            $date->sub(new DateInterval('P'.$Reminderinvoice->day.'D'));
            $date = $date->format('Y-m-d');
            $Reminderinvoice->date  = $date;
            $Reminderinvoice->save();
        }

    }
}
