<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DeleteVisitorDocument
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $visitor_document;
    public function __construct($visitor_document)
    {
        $this->visitor_document = $visitor_document;
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
