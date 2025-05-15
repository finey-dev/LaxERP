<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Changecouriertrackingstatus
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $courierInfo;
    public $courierTracking;
    public $request;
    public function __construct($courierInfo, $courierTracking,$request)
    {
        $this->courierInfo = $courierInfo;
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
