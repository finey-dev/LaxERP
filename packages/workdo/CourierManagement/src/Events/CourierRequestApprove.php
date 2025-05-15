<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class CourierRequestApprove
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $trackingId;
    public function __construct($trackingId)
    {
        $this->trackingId = $trackingId;
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
