<?php

namespace Workdo\SalesAgent\Listeners;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SalesAgent\Entities\SalesAgentPurchase;
use Workdo\SalesAgent\Entities\Customer;

class CreateInvoiceLis
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

    public function handle($event)
    {       
        if (module_is_active('SalesAgent')) {
            $invoice = $event->invoice;
            $request = $event->request;
            $user = Customer::where('user_id', $request->customer_id)->first();

            if (isset($request->agentPurchaseOrderId)) {
                $SalesAgentPurchase              = SalesAgentPurchase::find($request->agentPurchaseOrderId);
                $SalesAgentPurchase->invoice_id  = $invoice->id;
                $SalesAgentPurchase->save();
            }
        }
    }
}
