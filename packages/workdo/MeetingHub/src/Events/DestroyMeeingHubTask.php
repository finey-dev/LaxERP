<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubTask
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $task;

    public function __construct($task)
    {
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
