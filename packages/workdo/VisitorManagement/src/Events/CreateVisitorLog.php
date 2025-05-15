<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreateVisitorLog
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $visitorLog;
    public function __construct($request,$visitorLog)
    {
        $this->request = $request;
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
