<?php

namespace Workdo\Reminder\Events;

use Illuminate\Queue\SerializesModels;

class DestroyReminder
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $reminder;

    public function __construct($reminder)
    {
        $this->reminder = $reminder;
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
