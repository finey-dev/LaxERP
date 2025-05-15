<?php

namespace Workdo\MeetingHub\Events;

use Illuminate\Queue\SerializesModels;

class MeetingHubTwilioMsg
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $phoneNumber;
    public $message;
    
    public function __construct($phoneNumber,$message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
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
