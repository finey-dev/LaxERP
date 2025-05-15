<?php

namespace Workdo\TimeTracker\Events;

use Illuminate\Queue\SerializesModels;

class UpdateTimeTracker
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $tracker;
    public function __construct($request,$tracker)
    {
        $this->request = $request;
        $this->tracker = $tracker;
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
