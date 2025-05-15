<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Couriertrackingstatuschange
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courierTracking;
    public $request;
    public function __construct($courierTracking,$request)
    {
        $this->courierTracking = $courierTracking;
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
