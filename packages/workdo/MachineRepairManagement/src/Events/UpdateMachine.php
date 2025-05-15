<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateMachine
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $machine;

    public function __construct($request,$machine)
    {
        $this->request = $request;
        $this->machine = $machine;
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
