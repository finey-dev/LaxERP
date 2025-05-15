<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitorIncident
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $visitor_incident;
    public function __construct($visitor_incident)
    {
        $this->visitor_incident = $visitor_incident;
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
