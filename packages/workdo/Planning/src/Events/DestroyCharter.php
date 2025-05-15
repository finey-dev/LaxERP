<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class DestroyCharter
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Charter;

    public function __construct($Charter)
    {
        $this->Charter = $Charter;
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
