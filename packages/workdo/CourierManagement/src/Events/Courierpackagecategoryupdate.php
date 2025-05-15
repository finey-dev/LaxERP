<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Courierpackagecategoryupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $packageCategory;
    public $request;
    public function __construct($packageCategory,$request)
    {
        $this->packageCategory = $packageCategory;
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
