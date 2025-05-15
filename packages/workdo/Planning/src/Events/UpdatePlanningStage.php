<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class UpdatePlanningStage
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $Planningstages;

    public function __construct($request, $Planningstages)
    {
        $this->request = $request;
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
