<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class RfxApplicationChangeOrder
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $application;

    public function __construct($request, $application)
    {
        $this->request = $request;
        $this->application = $application;
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
