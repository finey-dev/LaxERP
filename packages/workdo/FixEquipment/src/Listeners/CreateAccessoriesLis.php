<?php

namespace Workdo\FixEquipment\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Entities\AccountUtility;
use Workdo\FixEquipment\Events\CreateAccessories;

class CreateAccessoriesLis
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
    public function handle(CreateAccessories $event)
    {
        if(module_is_active('Account'))
        {
            $request = $event->request;
            $totalamoubnt = $request->price * $request->quantity;
            // for chart of accounts data save
            $data = [
                'account_id' => !empty($request->account)? $request->account : 0 ,
                'transaction_type' => 'Debit',
                'transaction_amount' => $totalamoubnt,
                'reference' => 'Create Accessories',
                'reference_id' => $event->accessories->id,
                'reference_sub_id' => $event->accessories->id,
                'date' => $request->purchase_date ?? date('Y-m-d'),
            ];
            AccountUtility::addTransactionLines($data);
        }
    }
}
