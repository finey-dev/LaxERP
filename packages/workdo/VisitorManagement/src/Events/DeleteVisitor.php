<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitor
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $visitor;
    public function __construct($visitor)
    {
        $this->visitor = $visitor;
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
