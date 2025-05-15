<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreatePreRegistration
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $pre_registration;
    public function __construct($request,$pre_registration)
    {
        $this->request = $request;
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
