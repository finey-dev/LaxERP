<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRepairWarranty
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $repair_warranty;

    public function __construct($repair_warranty)
    {
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
