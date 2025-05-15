<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class CourierContractsupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courier_contracts;
    public $request;
    public function __construct($courier_contracts,$request)
    {
        $this->courier_contracts = $courier_contracts;
        $this->request = $request;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
