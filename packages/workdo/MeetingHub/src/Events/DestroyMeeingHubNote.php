<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubNote
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $meeting_minute_note;

    public function __construct($meeting_minute_note)
    {
        $this->meeting_minute_note = $meeting_minute_note;
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
