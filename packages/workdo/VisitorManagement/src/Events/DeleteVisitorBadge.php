<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitorBadge
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $visitor_badge;
    public function __construct($visitor_badge)
    {
        $this->visitor_badge = $visitor_badge;
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
