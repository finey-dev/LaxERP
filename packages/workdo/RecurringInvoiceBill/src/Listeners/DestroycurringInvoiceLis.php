<?php

namespace Workdo\RecurringInvoiceBill\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;

use App\Events\DestroyInvoice;


class DestroycurringInvoiceLis
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
    public function handle(DestroyInvoice $event)
    {
       $DestroycurringInvoice = RecurringInvoiceBill::where('invoice_id',$event->invoice->id)->where('recurring_type','invoice')->where('workspace' ,getActiveWorkSpace())->where('created_by' ,creatorId())->first();
        if(!empty($DestroycurringInvoice)){
            $DestroycurringInvoice->delete();
        }
    }
}
