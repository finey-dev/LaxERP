<?php

namespace Workdo\Performance\Events;

use Illuminate\Queue\SerializesModels;

class CreateGoalTracking
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $goalTracking;
    public function __construct($request, $goalTracking)
    {
        $this->request         = $request;
        $this->goalTracking = $goalTracking;
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
