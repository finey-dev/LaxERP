<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class CreateMeeingHubMeetingMinute
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $meeting_minute;
    
    public function __construct($request,$meeting_minute)
    {
        $this->request = $request;
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
