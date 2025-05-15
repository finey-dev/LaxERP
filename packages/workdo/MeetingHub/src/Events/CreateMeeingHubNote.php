<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class CreateMeeingHubNote
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $notes;
    
    public function __construct($request,$notes)
    {
        $this->request = $request;
        $this->notes = $notes;
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
