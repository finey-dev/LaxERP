<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubMeetingMinute
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $meeting_minute;

    public function __construct($meeting_minute)
    {
        $this->meeting_minute = $meeting_minute;
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
