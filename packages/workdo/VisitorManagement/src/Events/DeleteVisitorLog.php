<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitorLog
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $visitorLog;
    public function __construct($visitorLog)
    {
        $this->visitorLog = $visitorLog;
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
