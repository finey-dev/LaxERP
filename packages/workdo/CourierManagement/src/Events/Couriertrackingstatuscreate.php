<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Couriertrackingstatuscreate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $trackingStatus;
    public $request;
    public function __construct($trackingStatus, $request)
    {
        $this->trackingStatus = $trackingStatus;
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
