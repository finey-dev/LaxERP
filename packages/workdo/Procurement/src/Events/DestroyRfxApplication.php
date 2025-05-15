<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRfxApplication
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $rfxApplication;
    public function __construct($rfxApplication)
    {
        $this->rfxApplication = $rfxApplication;
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
