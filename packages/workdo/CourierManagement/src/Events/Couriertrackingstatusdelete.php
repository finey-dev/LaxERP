<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Couriertrackingstatusdelete
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $trackingStatusData;
    public $request;
    public function __construct($trackingStatusData, $request)
    {
        $this->trackingStatusData = $trackingStatusData;
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
