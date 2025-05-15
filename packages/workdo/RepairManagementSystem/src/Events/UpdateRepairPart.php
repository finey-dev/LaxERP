<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class UpdateRepairPart
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $repair_part;
    
    public function __construct($request,$repair_part)
    {
        $this->request = $request;
        $this->repair_part = $repair_part;
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
