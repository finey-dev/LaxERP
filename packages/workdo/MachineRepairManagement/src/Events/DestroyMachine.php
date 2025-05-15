<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMachine
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $machine;

    public function __construct($machine)
    {
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
