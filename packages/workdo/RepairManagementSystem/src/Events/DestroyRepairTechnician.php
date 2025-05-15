<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRepairTechnician
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $repair_technician;

    public function __construct($repair_technician)
    {
        $this->repair_technician = $repair_technician;
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
