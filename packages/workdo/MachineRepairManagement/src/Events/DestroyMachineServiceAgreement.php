<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMachineServiceAgreement
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $machineserviceagreement;

    public function __construct($machineserviceagreement)
    {
        $this->machineserviceagreement = $machineserviceagreement;
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
