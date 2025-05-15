<?php

namespace Workdo\BusinessProcessMapping\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBusinessProcessMapping
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $businessProcess;

    public function __construct($businessProcess)
    {
        $this->businessProcess = $businessProcess;
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
