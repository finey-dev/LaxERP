<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Courierservicetypeupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $servicetypeData;
    public $request;
    public function __construct($servicetypeData, $request)
    {
        $this->servicetypeData = $servicetypeData;
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
