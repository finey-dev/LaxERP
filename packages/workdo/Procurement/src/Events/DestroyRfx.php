<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRfx
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    
    public $rfx;

    public function __construct($rfx)
    {
        $this->rfx = $rfx;
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
