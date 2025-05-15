<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitorCompliance
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $visitor_compliance;
    public function __construct($visitor_compliance)
    {
        $this->visitor_compliance = $visitor_compliance;
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
