<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPlanningStage
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Planningstages;

    public function __construct($Planningstages)
    {
        $this->Planningstages = $Planningstages;
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
