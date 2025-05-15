<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeletePreRegistration
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $pre_registration;
    public function __construct($pre_registration)
    {
        $this->pre_registration = $pre_registration;
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
