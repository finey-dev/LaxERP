<?php

namespace Workdo\FixEquipment\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Entities\AccountUtility;
use Workdo\FixEquipment\Events\CreateLicence;

class CreateLicenceLis
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
    public function handle(CreateLicence $event)
    {
        if(module_is_active('Account'))
        {
            $request = $event->request;

            // for chart of accounts data save
            $data = [
                'account_id' => !empty($request->account)? $request->account : 0 ,
                'transaction_type' => 'Debit',
                'transaction_amount' => $request->purchase_price,
                'reference' => 'Create License',
                'reference_id' => $event->license->id,
                'reference_sub_id' => $event->license->id,
                'date' => $request->purchase_date,
            ];
            AccountUtility::addTransactionLines($data);
        }
    }
}
