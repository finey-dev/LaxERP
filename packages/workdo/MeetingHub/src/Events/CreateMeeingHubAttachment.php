<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class CreateMeeingHubAttachment
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $meeting_minute;
    public $file;
    
    public function __construct($meeting_minute,$file)
    {
        $this->meeting_minute = $meeting_minute;
        $this->file = $file;
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
