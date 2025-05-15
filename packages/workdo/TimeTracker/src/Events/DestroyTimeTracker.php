<?php

namespace Workdo\TimeTracker\Events;

use Illuminate\Queue\SerializesModels;

class DestroyTimeTracker
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $timetrecker;
    public function __construct($timetrecker)
    {
        $this->timetrecker = $timetrecker;
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
