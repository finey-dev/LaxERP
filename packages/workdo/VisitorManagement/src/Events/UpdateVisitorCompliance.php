<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateVisitorCompliance
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $visitor_compliance;
    public function __construct($request,$visitor_compliance)
    {
        $this->request = $request;
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
