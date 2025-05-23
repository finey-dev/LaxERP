<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class CreateMeeingHubTask
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $task;
    
    public function __construct($request,$task)
    {
        $this->request = $request;
        $this->task = $task;
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
