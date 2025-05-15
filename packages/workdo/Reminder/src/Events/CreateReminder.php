<?php

namespace Workdo\Reminder\Events;

use Illuminate\Queue\SerializesModels;

class CreateReminder
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $reminder;

    public function __construct($request ,$reminder)
    {
        $this->request = $request;
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
