<?php

namespace Workdo\TimeTracker\Events;

use Illuminate\Queue\SerializesModels;

class CreateTimeTracker
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $track;
    public function __construct($request,$track)
    {
        $this->request = $request;
        $this->track = $track;
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
