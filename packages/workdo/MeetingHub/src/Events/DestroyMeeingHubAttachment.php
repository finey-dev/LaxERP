<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class DestroyMeeingHubAttachment
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $file;

    public function __construct($file)
    {
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
