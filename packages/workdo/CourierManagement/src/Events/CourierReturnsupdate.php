<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class CourierReturnsupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courier_returns;
    public $request;
    public function __construct($courier_returns,$request)
    {
        $this->courier_returns = $courier_returns;
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
