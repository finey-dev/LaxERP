<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class CreateMeeingHubMeetingType
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $meetingtype;
    
    public function __construct($request,$meetingtype)
    {
        $this->request = $request;
        $this->meetingtype = $meetingtype;
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
