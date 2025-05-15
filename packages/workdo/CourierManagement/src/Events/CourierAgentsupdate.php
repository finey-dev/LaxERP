<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class CourierAgentsupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courier_agents;
    public $request;
    public function __construct($courier_agents,$request)
    {
        $this->courier_agents = $courier_agents;
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
