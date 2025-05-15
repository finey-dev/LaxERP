<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Courierbranchcreate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $branchData;
    public $request;
    public function __construct($branchData,$request)
    {
        $this->branchData = $branchData;
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
