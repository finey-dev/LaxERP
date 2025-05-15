<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPlanningStatus
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Planningstatus;

    public function __construct($Planningstatus)
    {
        $this->Planningstatus = $Planningstatus;
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
