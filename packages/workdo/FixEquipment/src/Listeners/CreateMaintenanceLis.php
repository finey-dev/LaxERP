<?php

namespace Workdo\FixEquipment\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Entities\AccountUtility;
use Workdo\FixEquipment\Events\CreateMaintenance;

class CreateMaintenanceLis
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
    public function handle(CreateMaintenance $event)
    {
        if(module_is_active('Account'))
        {
            $request = $event->request;
            // for chart of accounts data save
            $data = [
                'account_id' => !empty($request->account)? $request->account : 0 ,
                'transaction_type' => 'Debit',
                'transaction_amount' => $request->price,
                'reference' => 'Create Maintenance',
                'reference_id' => $event->maintenance->id,
                'reference_sub_id' => $event->maintenance->id,
                'date' => $request->maintenance_date ?? date('Y-m-d'),
            ];
            AccountUtility::addTransactionLines($data);
        }
    }
}
