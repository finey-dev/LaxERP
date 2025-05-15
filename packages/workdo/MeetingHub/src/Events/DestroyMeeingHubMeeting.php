<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubMeeting
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $meeting;

    public function __construct($meeting)
    {
        $this->meeting = $meeting;
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
