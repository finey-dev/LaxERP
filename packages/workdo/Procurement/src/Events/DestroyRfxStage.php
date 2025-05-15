<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRfxStage
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $rfxStage;
    public function __construct($rfxStage)
    {
        $this->rfxStage = $rfxStage;
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
