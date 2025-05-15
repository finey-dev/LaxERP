<?php

namespace Workdo\RecurringInvoiceBill\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\DestroyBill;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;

class DestroycurringBillLis
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
    public function handle(DestroyBill $event)
    {
        $DestroycurringInvoice = RecurringInvoiceBill::where('invoice_id',$event->bill->id)->where('workspace' ,getActiveWorkSpace())->where('recurring_type','bill')->where('created_by' ,creatorId())->first();

        if(!empty($DestroycurringInvoice)){
            $DestroycurringInvoice->delete();
        }
    }
}
