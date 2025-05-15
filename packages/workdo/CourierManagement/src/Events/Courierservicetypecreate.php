<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Courierservicetypecreate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $serviceType;
    public $request;
    public function __construct($serviceType,$request)
    {
        $this->serviceType = $serviceType;
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
