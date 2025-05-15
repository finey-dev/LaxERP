<?php

namespace Workdo\Reminder\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Lead\Events\UpdateLead;
use Workdo\Reminder\Entities\Reminder;
use DateTime;
use DateInterval;

class UpdateLeadLis
{
    /**
     * Update the event listener.
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
    public function handle(UpdateLead $event)
    {
        $lead = $event->lead;
        $Reminderinvoice = Reminder::where('module_value' ,$lead->id)->first();
        if(!empty($Reminderinvoice)){
            $dateString = $lead->follow_up_date;
            $date = new DateTime($dateString);
            $date->sub(new DateInterval('P'.$Reminderinvoice->day.'D'));
            $date = $date->format('Y-m-d');
            $Reminderinvoice->date  = $date;
            $Reminderinvoice->save();
        }
    }
}
