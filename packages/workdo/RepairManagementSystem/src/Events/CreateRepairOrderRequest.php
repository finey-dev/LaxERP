<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class CreateRepairOrderRequest
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $request;
    public $repair_order_request;
    
    public function __construct($request,$repair_order_request)
    {
        $this->request = $request;
        $this->repair_order_request = $repair_order_request;
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
