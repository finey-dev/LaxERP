<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class CreateRepairWarranty
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $repair_warranty;

    public function __construct($request,$repair_warranty)
    {
        $this->request = $request;
        $this->repair_warranty = $repair_warranty;
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
