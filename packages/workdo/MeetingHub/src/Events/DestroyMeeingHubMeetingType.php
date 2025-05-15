<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubMeetingType
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $meetingType;

    public function __construct($meetingType)
    {
        $this->meetingType = $meetingType;
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
