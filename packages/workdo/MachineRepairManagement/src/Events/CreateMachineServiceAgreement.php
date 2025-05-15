<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreateMachineServiceAgreement
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $machineserviceagreement;

    public function __construct($request,$machineserviceagreement)
    {
        $this->request = $request;
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
