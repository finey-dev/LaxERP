<?php

namespace Workdo\Reminder\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\UpdateInvoice;
use App\Models\Invoice;
use Workdo\Reminder\Entities\Reminder;
use DateTime;
use DateInterval;

class UpdateInvoiceLis
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
    public function handle(UpdateInvoice $event)
    {
        //
        $invoice = $event->invoice;
        $Reminderinvoice = Reminder::where('module_value' ,$invoice->id)->first();
        if(!empty($Reminderinvoice)){
            $dateString = $invoice->due_date;
            $date = new DateTime($dateString);
            $date->sub(new DateInterval('P'.$Reminderinvoice->day.'D'));
            $date = $date->format('Y-m-d');
            $Reminderinvoice->date  = $date;
            $Reminderinvoice->save();
        }



    }
}
